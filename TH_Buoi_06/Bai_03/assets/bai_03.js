Vue.createApp({
    data() {
        return {
            tenCongViecMoi: '',
            boLoc: 'tat-ca',
            idTiepTheo: 7,
            // Mỗi công việc có id riêng, tên và trạng thái hoàn thành.
            danhSachCongViec: [
                { id: 1, ten: 'Làm bài thực hành 6 của web', daHoanThanh: true },
                { id: 2, ten: 'Dịch bài tiếng anh TEST 01', daHoanThanh: true },
                { id: 3, ten: 'Speaking topic 4', daHoanThanh: true },
                { id: 4, ten: 'Triển khai Openstack bằng Kolla-Ansible', daHoanThanh: false },
                { id: 5, ten: 'Hoàn thành project Machine Learning', daHoanThanh: false },
                { id: 6, ten: 'Làm bài về nhà môn mật mã', daHoanThanh: false }
            ]
        };
    },
    computed: {
        tenCongViecHopLe() {
            return this.tenCongViecMoi.trim().length > 0;
        },
        tongSoCongViec() {
            return this.danhSachCongViec.length;
        },
        soViecChuaHoanThanh() {
            return this.danhSachCongViec.filter(congViec => !congViec.daHoanThanh).length;
        },
        soViecDaHoanThanh() {
            return this.tongSoCongViec - this.soViecChuaHoanThanh;
        },
        danhSachDaLoc() {
            if (this.boLoc === 'chua-hoan-thanh') {
                return this.danhSachCongViec.filter(congViec => !congViec.daHoanThanh);
            }
            if (this.boLoc === 'da-hoan-thanh') {
                return this.danhSachCongViec.filter(congViec => congViec.daHoanThanh);
            }
            return this.danhSachCongViec;
        },
        tiLeHoanThanh() {
            return this.tongSoCongViec === 0 ? 0
                : Math.round(this.soViecDaHoanThanh / this.tongSoCongViec * 100);
        },
        thongBaoRong() {
            if (this.tongSoCongViec === 0) {
                return { tieuDe: 'Chưa có công việc nào', noiDung: 'Thêm công việc đầu tiên để bắt đầu ngày của bạn.' };
            }
            if (this.boLoc === 'chua-hoan-thanh') {
                return { tieuDe: 'Bạn đã hoàn thành tất cả!', noiDung: 'Không còn công việc nào chưa hoàn thành.' };
            }
            return { tieuDe: 'Chưa có công việc đã hoàn thành', noiDung: 'Đánh dấu công việc đã làm xong để xem tại đây.' };
        }
    },
    methods: {
        themCongViec() {
            // Kiểm tra lại để chặn tên rỗng cả khi gửi bằng Enter.
            const ten = this.tenCongViecMoi.trim();
            if (!ten) return;
            this.danhSachCongViec.push({ id: this.idTiepTheo++, ten, daHoanThanh: false });
            this.tenCongViecMoi = '';
            // Hiện công việc mới ngay cả khi trước đó đang lọc việc đã hoàn thành.
            this.boLoc = 'tat-ca';
            this.$refs.oNhapCongViec.focus();
        },
        xoaCongViec(id) {
            this.danhSachCongViec = this.danhSachCongViec.filter(congViec => congViec.id !== id);
        },
        xoaCacViecDaHoanThanh() {
            this.danhSachCongViec = this.danhSachCongViec.filter(congViec => !congViec.daHoanThanh);
        }
    }
}).mount('#app');
