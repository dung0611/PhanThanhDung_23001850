"""Integration checks against the real PHP/MySQL API; Python standard library only."""
import json
import sys
import uuid
from urllib.error import HTTPError
from urllib.parse import urljoin
from urllib.request import Request, urlopen


def main():
    base = sys.argv[1] if len(sys.argv) > 1 else 'http://localhost/TH_Buoi_08/'
    endpoint = urljoin(base.rstrip('/') + '/', 'api/products.php')

    def request(method='GET', body=None, product_id=None, raw=None):
        url = endpoint if product_id is None else f'{endpoint}?id={product_id}'
        payload = raw if raw is not None else (None if body is None else json.dumps(body).encode('utf-8'))
        req = Request(url, data=payload, method=method, headers={'Content-Type': 'application/json'})
        try:
            response = urlopen(req, timeout=15)
        except HTTPError as error:
            response = error
        with response:
            assert 'application/json' in response.headers.get('Content-Type', ''), 'Response must be JSON'
            result = json.load(response)
            assert isinstance(result.get('success'), bool), 'Missing boolean success'
            return response.status, result

    def products():
        status, result = request()
        assert status == 200 and result['success'], result
        assert isinstance(result['data'], list), result
        return result['data']

    def check_error(method, expected, **kwargs):
        status, result = request(method, **kwargs)
        assert status == expected and result['success'] is False and result.get('message'), (status, result)

    marker = 'TH08-test-' + uuid.uuid4().hex[:12]
    product = {'name': marker, 'price': 6000, 'stock': 3}
    created_id = None
    try:
        products()  # Verify the server and database are available first.
        status, result = request('POST', {**product, 'name': '  ' + marker + '  '})
        assert status == 201 and result['success'], (status, result)
        created_id = result['data']['id']
        stored = next(row for row in products() if row['id'] == created_id)
        assert stored == {'id': created_id, **product}, stored
        print('PASS: POST persists and trims the name; a fresh GET returns the product.')

        product['price'] = 9000
        status, result = request('PUT', product, created_id)
        assert status == 200 and result['success'], result
        assert next(row for row in products() if row['id'] == created_id)['price'] == 9000
        status, result = request('PUT', product, created_id)
        assert status == 200 and result['success'], 'Saving unchanged values must succeed'
        print('PASS: PUT persists a new price; unchanged values also succeed.')

        before = products()
        invalid_products = [
            {**product, 'stock': -1}, {**product, 'stock': 1.5},
            {**product, 'stock': '3'}, {**product, 'stock': True},
            {**product, 'price': 0}, {**product, 'price': 1.5},
            {**product, 'price': '6000'}, {**product, 'price': 2147483648},
            {**product, 'name': '   '}, {**product, 'name': 'á' * 101},
            {**product, 'name': None}, {'name': marker, 'price': 6000},
        ]
        for invalid in invalid_products:
            check_error('POST', 422, body=invalid)
        check_error('PUT', 422, body={**product, 'stock': -1}, product_id=created_id)
        check_error('POST', 400, raw=b'{invalid json')
        check_error('POST', 400, body=[])
        check_error('DELETE', 400, product_id='abc')
        check_error('PATCH', 405, body=product)
        assert products() == before, 'Invalid requests changed MySQL data'
        print('PASS: Invalid direct requests, including stock -1, leave MySQL data unchanged.')

        status, result = request('DELETE', product_id=created_id)
        assert status == 200 and result['success'], result
        assert all(row['id'] != created_id for row in products())
        check_error('DELETE', 404, product_id=created_id)
        check_error('PUT', 404, body=product, product_id=created_id)
        created_id = None
        print('PASS: DELETE persists; updating or deleting the removed ID returns 404.')
        print('All API checks passed.')
    finally:
        if created_id is not None:
            request('DELETE', product_id=created_id)


if __name__ == '__main__':
    try:
        main()
    except Exception as error:
        print(f'FAIL: {error}', file=sys.stderr)
        sys.exit(1)
