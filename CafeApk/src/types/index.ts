export interface BahanBaku {
  id: number;
  nama: string;
  stok: number; // satuan gram atau ml atau porsi
  satuan: string;
}

export interface ResepItem {
  bahanId: number;
  jumlah: number;
}

export interface MenuItem {
  id: number;
  nama: string;
  harga: number;
  kategori: 'minuman' | 'makanan';
  status?: 'tersedia' | 'habis';
  resep: ResepItem[]; // kebutuhan bahan baku
}

export interface UserAccount {
  id: number;
  nama: string;
  nomorHp: string;
  password?: string;
  role: 'pelanggan' | 'kasir';
  mustChangePassword?: boolean;
  registeredAt: string;
}

export interface OrderItemDetail {
  menuId: number;
  nama: string;
  qty: number;
  harga: number;
}

export interface Order {
  id: number;
  kode: string;
  pelangganHp: string;
  pelangganNama: string;
  meja: string;
  metodePembayaran: 'Tunai' | 'QRIS';
  items: OrderItemDetail[];
  subtotal: number;
  serviceCharge: number;
  pajak: number;
  total: number;
  status: 'Baru' | 'Diproses' | 'Selesai' | 'Dibatalkan';
  createdAt: string;
}
