const API_BASE_URL = 'http://127.0.0.1:8000/api';

const getHeaders = (token?: string) => {
  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }
  return headers;
};

export const cafeApi = {
  // Auth
  login: async (nomer_telepon: string, password: string) => {
    const res = await fetch(`${API_BASE_URL}/auth/login`, {
      method: 'POST',
      headers: getHeaders(),
      body: JSON.stringify({ nomer_telepon, password }),
    });
    return res.json();
  },

  register: async (name: string, nomer_telepon: string, password: string) => {
    const res = await fetch(`${API_BASE_URL}/auth/register`, {
      method: 'POST',
      headers: getHeaders(),
      body: JSON.stringify({ name, nomer_telepon, password }),
    });
    return res.json();
  },

  changePassword: async (token: string, new_password: string, old_password?: string, is_forced?: boolean) => {
    const res = await fetch(`${API_BASE_URL}/auth/change-password`, {
      method: 'POST',
      headers: getHeaders(token),
      body: JSON.stringify({ new_password, old_password, is_forced }),
    });
    return res.json();
  },

  // Menus
  getMenus: async () => {
    const res = await fetch(`${API_BASE_URL}/menus`, {
      headers: getHeaders(),
    });
    return res.json();
  },

  // Orders
  getMyOrders: async (token: string) => {
    const res = await fetch(`${API_BASE_URL}/pesanan/my-orders`, {
      headers: getHeaders(token),
    });
    return res.json();
  },

  createOrder: async (
    token: string,
    data: {
      nomor_meja: number;
      metode_pesan: 'tunai' | 'qris';
      nama_pelanggan?: string;
      items: { menuId: number; qty: number }[];
    }
  ) => {
    const res = await fetch(`${API_BASE_URL}/pesanan`, {
      method: 'POST',
      headers: getHeaders(token),
      body: JSON.stringify(data),
    });
    return res.json();
  },

  updateStatus: async (token: string, orderId: number, status: 'baru' | 'diproses' | 'selesai' | 'dibatalkan') => {
    const res = await fetch(`${API_BASE_URL}/pesanan/${orderId}/status`, {
      method: 'PATCH',
      headers: getHeaders(token),
      body: JSON.stringify({ status }),
    });
    return res.json();
  },

  // Kasir Operations
  getAllOrders: async (token: string) => {
    const res = await fetch(`${API_BASE_URL}/kasir/orders`, {
      headers: getHeaders(token),
    });
    return res.json();
  },

  getCustomers: async (token: string) => {
    const res = await fetch(`${API_BASE_URL}/kasir/customers`, {
      headers: getHeaders(token),
    });
    return res.json();
  },

  resetCustomerPassword: async (token: string, nomer_telepon: string) => {
    const res = await fetch(`${API_BASE_URL}/kasir/reset-password`, {
      method: 'POST',
      headers: getHeaders(token),
      body: JSON.stringify({ nomer_telepon }),
    });
    return res.json();
  },

  getBahanBaku: async (token: string) => {
    const res = await fetch(`${API_BASE_URL}/kasir/bahan-baku`, {
      headers: getHeaders(token),
    });
    return res.json();
  },
};

