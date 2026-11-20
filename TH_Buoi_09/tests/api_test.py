"""Integration checks for the real PHP/MySQL API; Python standard library only."""
import base64
import json
import sys
import uuid
from urllib.error import HTTPError
from urllib.parse import urljoin
from urllib.request import Request, urlopen


def main():
    base = sys.argv[1] if len(sys.argv) > 1 else 'http://localhost/TH_Buoi_09/'
    products_url = urljoin(base.rstrip('/') + '/', 'api/products.php')
    upload_url = urljoin(base.rstrip('/') + '/', 'api/upload.php')

    def send(url, method='GET', body=None, content_type=None):
        headers = {} if content_type is None else {'Content-Type': content_type}
        request = Request(url, data=body, method=method, headers=headers)
        try:
            response = urlopen(request, timeout=20)
        except HTTPError as error:
            response = error
        with response:
            assert 'application/json' in response.headers.get('Content-Type', ''), 'Response must be JSON'
            return response.status, json.load(response)

    def api(method='GET', product_id=None, query='', body=None):
        url = products_url + (f'?id={product_id}' if product_id is not None else query)
        payload = None if body is None else json.dumps(body).encode('utf-8')
        return send(url, method, payload, 'application/json' if payload else None)

    def upload(product_id, filename, content_type, contents):
        boundary = '----TH09' + uuid.uuid4().hex
        body = (
            f'--{boundary}\r\nContent-Disposition: form-data; name="product_id"\r\n\r\n{product_id}\r\n'
            f'--{boundary}\r\nContent-Disposition: form-data; name="image"; filename="{filename}"\r\n'
            f'Content-Type: {content_type}\r\n\r\n'
        ).encode() + contents + f'\r\n--{boundary}--\r\n'.encode()
        return send(upload_url, 'POST', body, f'multipart/form-data; boundary={boundary}')

    def expect_error(result, code):
        status, data = result
        assert status == code and data.get('success') is False and data.get('message'), (status, data)

    png = base64.b64decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/lXcAAAAASUVORK5CYII='
    )
    marker = 'TH09-test-' + uuid.uuid4().hex[:10]
    product = {'name': marker, 'price': 6000, 'stock': 3}
    created_id = None
    try:
        status, result = api(query='?page=1&limit=5')
        assert status == 200 and result['success'], result
        assert len(result['data']) <= 5
        assert result['pagination']['limit'] == 5
        initial_total = result['pagination']['total']
        assert initial_total >= 12, 'Import database.sql to get at least 12 sample products'
        assert all(a['id'] > b['id'] for a, b in zip(result['data'], result['data'][1:]))
        status, middle = api(query='?page=2&limit=5')
        assert status == 200 and len(middle['data']) == 5
        assert result['data'][-1]['id'] > middle['data'][0]['id']
        status, last = api(query='?page=2147483647&limit=5')
        assert status == 200 and last['pagination']['page'] == last['pagination']['total_pages']
        assert len(last['data']) == initial_total % 5 or len(last['data']) == 5
        expect_error(api(query='?page=0'), 400)
        expect_error(api(query='?page=abc'), 400)
        expect_error(api(query='?limit=6'), 400)
        print('PASS: Backend pagination, descending order, final page and invalid parameters.')

        status, result = api('POST', body=product)
        assert status == 201 and result['success'] and isinstance(result['data']['id'], int)
        created_id = result['data']['id']
        status, result = api(query='?page=1&limit=5')
        assert status == 200 and result['pagination']['total'] == initial_total + 1
        assert result['data'][0]['id'] == created_id and result['data'][0]['image'] is None
        print('PASS: POST returns the new ID and refreshes page 1.')

        expect_error(upload(created_id, 'wrong.gif', 'image/gif', b'GIF89a' + b'\0' * 20), 422)
        expect_error(upload(created_id, 'big.png', 'image/png', png + b'\0' * (2 * 1024 * 1024)), 413)
        expect_error(upload(created_id, 'fake.png', 'image/png', b'not a real image'), 422)
        expect_error(upload(2147483647, 'image.png', 'image/png', png), 404)
        status, result = upload(created_id, 'image.png', 'image/png', png)
        assert status == 200 and result['success'] and result['data']['product_id'] == created_id
        image_path = result['data']['image']
        assert image_path.startswith('uploads/') and image_path.endswith('.png')
        status, result = api(query='?page=1&limit=5')
        assert result['data'][0]['image'] == image_path
        with urlopen(urljoin(base.rstrip('/') + '/', image_path), timeout=15) as response:
            assert response.read() == png
        print('PASS: Invalid content, wrong type and oversized files rejected; valid PNG saved and served.')

        status, result = api('PUT', product_id=created_id, body={**product, 'price': 9000})
        assert status == 200 and result['success']
        status, result = api(query='?page=1&limit=5')
        assert result['data'][0]['price'] == 9000 and result['data'][0]['image'] == image_path
        expect_error(api('POST', body={**product, 'stock': -1}), 422)
        print('PASS: Editing without a new image keeps the image; invalid JSON data is rejected.')

        status, result = api('DELETE', product_id=created_id)
        assert status == 200 and result['success']
        created_id = None
        status, result = api(query='?page=1&limit=5')
        assert result['pagination']['total'] == initial_total
        print('PASS: Deleting restores the total.')
    finally:
        if created_id is not None:
            api('DELETE', product_id=created_id)


if __name__ == '__main__':
    try:
        main()
    except Exception as error:
        print(f'FAIL: {error}', file=sys.stderr)
        sys.exit(1)
