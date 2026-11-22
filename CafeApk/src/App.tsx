import React, { useState } from 'react';
import { IonApp, setupIonicReact } from '@ionic/react';
import { INITIAL_MENUS, INITIAL_PESANAN, MenuItem, PesananItem } from './mockData';

import AuthPage from './pages/AuthPage';
import CustomerMenuPage from './pages/CustomerMenuPage';
import CustomerOrderPage from './pages/CustomerOrderPage';
import CustomerHistoryPage from './pages/CustomerHistoryPage';
import OrderSuccessPage from './pages/OrderSuccessPage';
import CashierDashboardPage from './pages/CashierDashboardPage';

/* Core CSS required for Ionic components to work properly */
import '@ionic/react/css/core.css';
import '@ionic/react/css/normalize.css';
import '@ionic/react/css/structure.css';
import '@ionic/react/css/typography.css';
import './theme/variables.css';

setupIonicReact();

const App: React.FC = () => {
  // Authentication & Role State
  const [userRole, setUserRole] = useState<'guest' | 'pelanggan' | 'kasir'>('pelanggan');

  // Customer Navigation State
  const [customerTab, setCustomerTab] = useState<'menu' | 'pesan' | 'riwayat'>('menu');
  const [latestOrder, setLatestOrder] = useState<PesananItem | null>(null);

  // App Data State
  const [menus] = useState<MenuItem[]>(INITIAL_MENUS);
  const [orders, setOrders] = useState<PesananItem[]>(INITIAL_PESANAN);

  const handleLoginSuccess = (role: 'kasir' | 'pelanggan') => {
    setUserRole(role);
    setCustomerTab('menu');
    setLatestOrder(null);
  };

  const handleLogout = () => {
    setUserRole('guest');
    setLatestOrder(null);
  };

  const handleNewOrder = (newOrder: PesananItem) => {
    setOrders((prev) => [newOrder, ...prev]);
    setLatestOrder(newOrder);
  };

  const handleUpdateOrderStatus = (
    orderId: number,
    nextStatus: 'Diproses' | 'Selesai' | 'Dibatalkan'
  ) => {
    setOrders((prev) =>
      prev.map((o) => (o.id === orderId ? { ...o, status: nextStatus } : o))
    );
  };

  return (
    <IonApp>
      {/* 1. GUEST: LOGIN / DAFTAR */}
      {userRole === 'guest' && (
        <AuthPage onLoginSuccess={handleLoginSuccess} />
      )}

      {/* 2. ROLE: KASIR */}
      {userRole === 'kasir' && (
        <CashierDashboardPage
          orders={orders}
          menus={menus}
          onUpdateStatus={handleUpdateOrderStatus}
          onLogout={handleLogout}
        />
      )}

      {/* 3. ROLE: PELANGGAN */}
      {userRole === 'pelanggan' && (
        <>
          {latestOrder ? (
            <OrderSuccessPage
              order={latestOrder}
              onBack={() => {
                setLatestOrder(null);
                setCustomerTab('riwayat');
              }}
            />
          ) : (
            <>
              {customerTab === 'menu' && (
                <CustomerMenuPage
                  menus={menus}
                  onOrderNow={() => setCustomerTab('pesan')}
                  onLogout={handleLogout}
                />
              )}

              {customerTab === 'pesan' && (
                <CustomerOrderPage
                  menus={menus}
                  onOrderSuccess={handleNewOrder}
                  onLogout={handleLogout}
                />
              )}

              {customerTab === 'riwayat' && (
                <CustomerHistoryPage
                  orders={orders}
                  onLogout={handleLogout}
                />
              )}

              {/* Bottom Navigation Bar khas Mobile App */}
              <div className="senja-tabs">
                <button
                  type="button"
                  className={`tab-btn ${customerTab === 'menu' ? 'active' : ''}`}
                  onClick={() => setCustomerTab('menu')}
                >
                  <span style={{ fontSize: 18 }}>☕</span>
                  <span>Menu</span>
                </button>
                <button
                  type="button"
                  className={`tab-btn ${customerTab === 'pesan' ? 'active' : ''}`}
                  onClick={() => setCustomerTab('pesan')}
                >
                  <span style={{ fontSize: 18 }}>📝</span>
                  <span>Pesan</span>
                </button>
                <button
                  type="button"
                  className={`tab-btn ${customerTab === 'riwayat' ? 'active' : ''}`}
                  onClick={() => setCustomerTab('riwayat')}
                >
                  <span style={{ fontSize: 18 }}>📜</span>
                  <span>Riwayat</span>
                </button>
              </div>
            </>
          )}
        </>
      )}
    </IonApp>
  );
};

export default App;
