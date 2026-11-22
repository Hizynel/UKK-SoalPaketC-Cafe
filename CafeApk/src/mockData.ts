export interface MenuItem {
  id: number;
  nama: string;
  harga: number;
  kategori: 'minuman' | 'makanan';
  status: 'tersedia' | 'habis';
}

export interface CartItem {
  menu: MenuItem;
  qty: number;
}

export interface PesananItem {
  id: number;
  kode: string;
  pelanggan: string;
  nomor_hp?: string;
  meja: string;
  metode_pembayaran: 'Tunai' | 'QRIS';
  items: { nama: string; qty: number; harga: number }[];
  subtotal: number;
  service_charge: number;
  pajak: number;
  total: number;
  status: 'Baru' | 'Diproses' | 'Selesai' | 'Dibatalkan';
  waktu: string;
}

export const INITIAL_MENUS: MenuItem[] = [
  { id: 1, nama: 'Kopi Susu Senja', harga: 22000, kategori: 'minuman', status: 'tersedia' },
  { id: 2, nama: 'Americano', harga: 18000, kategori: 'minuman', status: 'tersedia' },
  { id: 3, nama: 'Teh Tarik', harga: 16000, kategori: 'minuman', status: 'habis' },
  { id: 4, nama: 'Roti Bakar Cokelat', harga: 20000, kategori: 'makanan', status: 'tersedia' },
  { id: 5, nama: 'Nasi Goreng Senja', harga: 28000, kategori: 'makanan', status: 'tersedia' },
  { id: 6, nama: 'Pisang Goreng', harga: 15000, kategori: 'makanan', status: 'tersedia' },
];

export const INITIAL_PESANAN: PesananItem[] = [
  {
    id: 1,
    kode: 'K7M2QX4A',
    pelanggan: 'Rina Wulandari',
    nomor_hp: '081234567890',
    meja: 'Meja 3',
    metode_pembayaran: 'Tunai',
    items: [
      { nama: 'Kopi Susu Senja', qty: 2, harga: 22000 },
      { nama: 'Roti Bakar Cokelat', qty: 1, harga: 20000 },
    ],
    subtotal: 64000,
    service_charge: 3200,
    pajak: 6720,
    total: 73920,
    status: 'Baru',
    waktu: '2 menit lalu',
  },
  {
    id: 2,
    kode: 'P9TR5WNB',
    pelanggan: 'Dimas Pratama',
    nomor_hp: '085711223344',
    meja: 'Meja 7',
    metode_pembayaran: 'QRIS',
    items: [{ nama: 'Americano', qty: 1, harga: 18000 }],
    subtotal: 35000,
    service_charge: 1750,
    pajak: 1750,
    total: 38500,
    status: 'Diproses',
    waktu: '5 menit lalu',
  },
  {
    id: 3,
    kode: 'H4XZ8C2D',
    pelanggan: 'Sari Anggraini',
    nomor_hp: '082198765432',
    meja: 'Meja 1',
    metode_pembayaran: 'QRIS',
    items: [
      { nama: 'Nasi Goreng Senja', qty: 2, harga: 28000 },
      { nama: 'Teh Manis', qty: 1, harga: 9000 },
    ],
    subtotal: 65000,
    service_charge: 3250,
    pajak: 3250,
    total: 71500,
    status: 'Selesai',
    waktu: '8 menit lalu',
  },
  {
    id: 4,
    kode: 'M3N6V9LE',
    pelanggan: 'Budi Santoso',
    nomor_hp: '081377889900',
    meja: 'Meja 5',
    metode_pembayaran: 'Tunai',
    items: [{ nama: 'Pisang Goreng', qty: 1, harga: 15000 }],
    subtotal: 25000,
    service_charge: 1250,
    pajak: 1250,
    total: 27500,
    status: 'Baru',
    waktu: '11 menit lalu',
  },
  {
    id: 5,
    kode: 'T8B2K5QY',
    pelanggan: 'Ayu Lestari',
    nomor_hp: '087855443322',
    meja: 'Meja 2',
    metode_pembayaran: 'Tunai',
    items: [{ nama: 'Kopi Susu Senja', qty: 2, harga: 22000 }],
    subtotal: 40000,
    service_charge: 2000,
    pajak: 2000,
    total: 44000,
    status: 'Selesai',
    waktu: '15 menit lalu',
  },
  {
    id: 6,
    kode: 'C5W7R2HJ',
    pelanggan: 'Eko Prasetyo',
    nomor_hp: '089912345678',
    meja: 'Meja 9',
    metode_pembayaran: 'Tunai',
    items: [{ nama: 'Nasi Goreng Senja', qty: 1, harga: 28000 }],
    subtotal: 30000,
    service_charge: 1500,
    pajak: 1500,
    total: 33000,
    status: 'Diproses',
    waktu: '20 menit lalu',
  },
];

