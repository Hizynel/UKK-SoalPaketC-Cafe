import React, { useState } from 'react';
import { IonApp, setupIonicReact } from '@ionic/react';
import { CafeProvider, useCafe } from './context/CafeContext';
import { AuthPage } from './auth/AuthPage';
import { ChangePasswordModal } from './auth/ChangePasswordModal';
import { CustomerMenuPage } from './pages/CustomerMenuPage';
import { CustomerOrderPage } from './pages/CustomerOrderPage';
import { CustomerHistoryPage } from './pages/CustomerHistoryPage';
import { OrderSuccessPage } from './pages/OrderSuccessPage';
import { CashierDashboardPage } from './pages/CashierDashboardPage';
import { CustomerTabs } from './components/CustomerTabs';
import { Order } from './types';

/* Core CSS required for Ionic components to work properly */
import '@ionic/react/css/core.css';
import '@ionic/react/css/normalize.css';
import '@ionic/react/css/structure.css';
import '@ionic/react/css/typography.css';
import './theme/variables.css';

setupIonicReact();

const MainNavigator: React.FC = () => {
  const { currentUser, hasActiveBaruOrder } = useCafe();

  // Navigation tab untuk customer
  const [customerTab, setCustomerTab] = useState<'menu' | 'pesan' | 'riwayat'>('menu');
  const [selectedMenuQuantities, setSelectedMenuQuantities] = useState<Record<number, number>>({});
  const [latestCreatedOrder, setLatestCreatedOrder] = useState<Order | null>(null);

  // Modal Ganti Password mandiri atau paksa (forced)
  const [showChangePasswordModal, setShowChangePasswordModal] = useState(false);
  const [isForcedChange, setIsForcedChange] = useState(false);

  // 1. Belum login
  if (!currentUser) {
    return (
      <AuthPage
        onNeedForceChangePassword={() => {
          setIsForcedChange(true);
          setShowChangePasswordModal(true);
        }}
      />
    );
  }

  // 2. Akun harus ganti password (karena baru direset kasir)
  if (currentUser.mustChangePassword || (showChangePasswordModal && isForcedChange)) {
    return (
      <ChangePasswordModal
        forced={true}
        onSuccess={() => {
          setIsForcedChange(false);
          setShowChangePasswordModal(false);
        }}
      />
    );
  }

  // 3. Modal ganti password mandiri
  if (showChangePasswordModal) {
    return (
      <ChangePasswordModal
        forced={false}
        onSuccess={() => setShowChangePasswordModal(false)}
        onCancel={() => setShowChangePasswordModal(false)}
      />
    );
  }

  // 4. Role: Kasir
  if (currentUser.role === 'kasir') {
    return <CashierDashboardPage />;
  }

  // 5. Role: Pelanggan
  const hasActiveOrder = hasActiveBaruOrder(currentUser.nomorHp);

  return (
    <>
      {/* Jika baru saja membuat pesanan, tampilkan halaman kode sukses unik */}
      {latestCreatedOrder ? (
        <OrderSuccessPage
          order={latestCreatedOrder}
          onBack={() => {
            setLatestCreatedOrder(null);
            setCustomerTab('riwayat');
          }}
        />
      ) : (
        <>
          {customerTab === 'menu' && (
            <CustomerMenuPage
              onSelectMenuToOrder={(menuId) => {
                setSelectedMenuQuantities({ [menuId]: 1 });
                setCustomerTab('pesan');
              }}
              onOpenChangePassword={() => setShowChangePasswordModal(true)}
            />
          )}

          {customerTab === 'pesan' && (
            <CustomerOrderPage
              initialQuantities={selectedMenuQuantities}
              onOrderSuccess={(order) => {
                setLatestCreatedOrder(order);
                setSelectedMenuQuantities({});
              }}
              onGoToHistory={() => setCustomerTab('riwayat')}
            />
          )}

          {customerTab === 'riwayat' && <CustomerHistoryPage />}

          {/* Reusable Customer Bottom Navigation */}
          <CustomerTabs
            activeTab={customerTab}
            onChangeTab={setCustomerTab}
            hasActiveOrder={hasActiveOrder}
          />
        </>
      )}
    </>
  );
};

const App: React.FC = () => {
  return (
    <IonApp>
      <CafeProvider>
        <MainNavigator />
      </CafeProvider>
    </IonApp>
  );
};

export default App;
