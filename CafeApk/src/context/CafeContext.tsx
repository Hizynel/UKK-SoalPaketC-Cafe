import React, { createContext, useContext, useState, useEffect, useCallback } from 'react';
import { UserAccount, MenuItem, BahanBaku, Order } from '../types';
import { cafeApi } from '../services/api';

interface CafeContextType {
  currentUser: UserAccount | null;
  authToken: string | null;
  users: UserAccount[];
  menus: MenuItem[];
  bahanBaku: BahanBaku[];
  orders: Order[];
  loading: boolean;
  
  // Auth methods (Poin 12)
  login: (nomorHp: string, pass: string) => Promise<{ success: boolean; message?: string; mustChange?: boolean }>;
  register: (nama: string, nomorHp: string, pass: string) => Promise<{ success: boolean; message?: string }>;
  logout: () => void;
  changePassword: (oldPass: string, newPass: string) => Promise<{ success: boolean; message?: string }>;
  forceChangePassword: (newPass: string) => Promise<{ success: boolean; message?: string }>;
  resetCustomerPasswordByCashier: (userHp: string) => Promise<{ success: boolean; tempPassword?: string }>;

  // Menu & Stok (Poin 13)
  checkStockAvailability: (items: { menuId: number; qty: number }[]) => { available: boolean; reason?: string };
  isMenuAvailable: (menuId: number) => boolean;
  refreshData: () => Promise<void>;

  // Order methods (Poin 13 & 14)
  hasActiveBaruOrder: (pelangganHp: string) => boolean;
  createOrder: (data: {
    pelangganHp: string;
    pelangganNama: string;
    meja: string;
    metodePembayaran: 'Tunai' | 'QRIS';
    items: { menuId: number; qty: number }[];
  }) => Promise<{ success: boolean; message?: string; order?: Order }>;
  updateOrderStatus: (orderId: number, nextStatus: 'Diproses' | 'Selesai' | 'Dibatalkan') => Promise<void>;
}

const CafeContext = createContext<CafeContextType | null>(null);

export const CafeProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [authToken, setAuthToken] = useState<string | null>(() => {
    return localStorage.getItem('cafe_auth_token');
  });

  const [currentUser, setCurrentUser] = useState<UserAccount | null>(() => {
    const saved = localStorage.getItem('cafe_current_user');
    return saved ? JSON.parse(saved) : null;
  });

  const [menus, setMenus] = useState<MenuItem[]>([]);
  const [bahanBaku, setBahanBaku] = useState<BahanBaku[]>([]);
  const [users, setUsers] = useState<UserAccount[]>([]);
  const [orders, setOrders] = useState<Order[]>([]);
  const [loading, setLoading] = useState(false);

  // Ambil data dari Backend Database
  const refreshData = useCallback(async () => {
    try {
      // 1. Ambil Menu (termasuk status stok dihitung dari db)
      const menuRes = await cafeApi.getMenus();
      if (menuRes.success) {
        setMenus(menuRes.data);
      }

      if (authToken) {
        // 2. Ambil Pesanan
        if (currentUser?.role === 'kasir') {
          const [orderRes, custRes, bahanRes] = await Promise.all([
            cafeApi.getAllOrders(authToken),
            cafeApi.getCustomers(authToken),
            cafeApi.getBahanBaku(authToken),
          ]);

          if (orderRes.message === 'Unauthenticated.' || custRes.message === 'Unauthenticated.') {
            logout();
            return;
          }

          if (orderRes.success) setOrders(orderRes.data);
          if (custRes.success) {
            setUsers(
              custRes.data.map((c: any) => ({
                id: c.id,
                nama: c.name,
                nomorHp: c.nomer_telepon,
                password: '••••••••',
                role: 'pelanggan',
                mustChangePassword: Boolean(c.must_change_password),
                registeredAt: new Date(c.created_at).toLocaleDateString('id-ID', {
                  day: '2-digit',
                  month: 'short',
                  year: 'numeric',
                }),
              }))
            );
          }
          if (bahanRes.success) setBahanBaku(bahanRes.data);
        } else {
          // Khusus Pelanggan (Poin 14: Hanya pesanan milik sendiri)
          const myOrderRes = await cafeApi.getMyOrders(authToken);
          if (myOrderRes.message === 'Unauthenticated.') {
            logout();
            return;
          }
          if (myOrderRes.success) {
            setOrders(myOrderRes.data);
          }
        }
      }
    } catch (err) {
      console.error('Error fetching data from API:', err);
    }
  }, [authToken, currentUser?.role]);

  useEffect(() => {
    refreshData();
  }, [refreshData]);

  // Cek ketersediaan menu
  const isMenuAvailable = (menuId: number): boolean => {
    const menu = menus.find((m) => m.id === menuId);
    return menu ? menu.status === 'tersedia' : false;
  };

  // Cek ketersediaan akumulatif bahan untuk keranjang
  const checkStockAvailability = (items: { menuId: number; qty: number }[]): { available: boolean; reason?: string } => {
    for (const item of items) {
      const menu = menus.find((m) => m.id === item.menuId);
      if (menu && menu.status === 'habis') {
        return { available: false, reason: `Menu ${menu.nama} sedang habis.` };
      }
    }
    return { available: true };
  };

  // Poin 12: Login API
  const login = async (nomorHp: string, pass: string) => {
    setLoading(true);
    try {
      const res = await cafeApi.login(nomorHp, pass);
      if (res.success) {
        const u: UserAccount = {
          id: res.user.id,
          nama: res.user.name,
          nomorHp: res.user.nomer_telepon,
          password: pass,
          role: res.user.role,
          mustChangePassword: res.user.must_change_password,
          registeredAt: 'Baru saja',
        };
        setAuthToken(res.token);
        setCurrentUser(u);
        localStorage.setItem('cafe_auth_token', res.token);
        localStorage.setItem('cafe_current_user', JSON.stringify(u));
        return { success: true, mustChange: res.mustChange };
      } else {
        return { success: false, message: res.message || 'Login gagal.' };
      }
    } catch (e: any) {
      return { success: false, message: 'Tidak dapat terhubung ke server Laravel API.' };
    } finally {
      setLoading(false);
    }
  };

  // Poin 12: Daftar Akun API
  const register = async (nama: string, nomorHp: string, pass: string) => {
    setLoading(true);
    try {
      const res = await cafeApi.register(nama, nomorHp, pass);
      if (res.success) {
        return { success: true };
      } else {
        return { success: false, message: res.message || 'Pendaftaran gagal.' };
      }
    } catch (e: any) {
      return { success: false, message: 'Tidak dapat terhubung ke server database.' };
    } finally {
      setLoading(false);
    }
  };

  const logout = () => {
    setAuthToken(null);
    setCurrentUser(null);
    setOrders([]);
    localStorage.removeItem('cafe_auth_token');
    localStorage.removeItem('cafe_current_user');
  };

  // Poin 12: Ganti Password Mandiri API
  const changePassword = async (oldPass: string, newPass: string) => {
    if (!authToken) return { success: false, message: 'Belum login.' };
    try {
      const res = await cafeApi.changePassword(authToken, newPass, oldPass, false);
      if (res.success) {
        if (currentUser) {
          const updated = { ...currentUser, mustChangePassword: false };
          setCurrentUser(updated);
          localStorage.setItem('cafe_current_user', JSON.stringify(updated));
        }
        return { success: true };
      } else {
        return { success: false, message: res.message };
      }
    } catch (err) {
      return { success: false, message: 'Gagal menghubungi server database.' };
    }
  };

  // Poin 12: Wajib ganti password saat login berikutnya API
  const forceChangePassword = async (newPass: string) => {
    if (!authToken) return { success: false, message: 'Belum login.' };
    try {
      const res = await cafeApi.changePassword(authToken, newPass, undefined, true);
      if (res.success) {
        if (currentUser) {
          const updated = { ...currentUser, mustChangePassword: false };
          setCurrentUser(updated);
          localStorage.setItem('cafe_current_user', JSON.stringify(updated));
        }
        return { success: true };
      } else {
        return { success: false, message: res.message };
      }
    } catch (err) {
      return { success: false, message: 'Gagal menghubungi server database.' };
    }
  };

  // Poin 12: Lupa password direset Kasir API
  const resetCustomerPasswordByCashier = async (userHp: string) => {
    if (!authToken) return { success: false };
    try {
      const res = await cafeApi.resetCustomerPassword(authToken, userHp);
      if (res.success) {
        await refreshData();
        return { success: true, tempPassword: res.temp_password };
      }
      return { success: false };
    } catch (err) {
      return { success: false };
    }
  };

  // Poin 13: Cek hanya boleh 1 pesanan baru
  const hasActiveBaruOrder = (pelangganHp: string): boolean => {
    return orders.some((o) => o.pelangganHp === pelangganHp && o.status === 'Baru');
  };

  // Poin 13: Buat pesanan ke database Laravel
  const createOrder = async (data: {
    pelangganHp: string;
    pelangganNama: string;
    meja: string;
    metodePembayaran: 'Tunai' | 'QRIS';
    items: { menuId: number; qty: number }[];
  }) => {
    if (!authToken) return { success: false, message: 'Belum terautentikasi.' };

    const tableNumber = parseInt(data.meja.replace(/\D/g, ''), 10) || 1;

    try {
      const res = await cafeApi.createOrder(authToken, {
        nomor_meja: tableNumber,
        metode_pesan: data.metodePembayaran.toLowerCase() as 'tunai' | 'qris',
        nama_pelanggan: data.pelangganNama,
        items: data.items,
      });

      if (res.message === 'Unauthenticated.' || !res.success && res.message?.includes('Unauthenticated')) {
        logout();
        return { success: false, message: 'Sesi Anda telah berakhir. Silakan login kembali.' };
      }

      if (res.success && res.data) {
        await refreshData();
        return { success: true, order: res.data };
      } else {
        return { success: false, message: res.message || 'Gagal membuat pesanan.' };
      }
    } catch (err: any) {
      return { success: false, message: 'Gagal menyimpan pesanan ke database.' };
    }
  };

  // Update status pesanan API
  const updateOrderStatus = async (
    orderId: number,
    nextStatus: 'Diproses' | 'Selesai' | 'Dibatalkan'
  ) => {
    if (!authToken) return;
    try {
      const apiStatus = nextStatus.toLowerCase() as 'baru' | 'diproses' | 'selesai' | 'dibatalkan';
      const res = await cafeApi.updateStatus(authToken, orderId, apiStatus);
      if (res.success) {
        await refreshData();
      } else {
        alert(res.message);
      }
    } catch (err) {
      console.error('Gagal update status pesanan:', err);
    }
  };

  return (
    <CafeContext.Provider
      value={{
        currentUser,
        authToken,
        users,
        menus,
        bahanBaku,
        orders,
        loading,
        login,
        register,
        logout,
        changePassword,
        forceChangePassword,
        resetCustomerPasswordByCashier,
        checkStockAvailability,
        isMenuAvailable,
        refreshData,
        hasActiveBaruOrder,
        createOrder,
        updateOrderStatus,
      }}
    >
      {children}
    </CafeContext.Provider>
  );
};

export const useCafe = () => {
  const context = useContext(CafeContext);
  if (!context) {
    throw new Error('useCafe must be used within a CafeProvider');
  }
  return context;
};
