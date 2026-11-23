import React from 'react';
import './CustomerTabs.css';

interface CustomerTabsProps {
  activeTab: 'menu' | 'pesan' | 'riwayat';
  onChangeTab: (tab: 'menu' | 'pesan' | 'riwayat') => void;
  hasActiveOrder?: boolean;
}

export const CustomerTabs: React.FC<CustomerTabsProps> = ({
  activeTab,
  onChangeTab,
  hasActiveOrder,
}) => {
  return (
    <nav className="customer-tabs-container">
      <button
        type="button"
        className={`customer-tab-btn ${activeTab === 'menu' ? 'active' : ''}`}
        onClick={() => onChangeTab('menu')}
      >
        <span className="tab-icon">☕</span>
        <span className="tab-text">Menu</span>
      </button>

      <button
        type="button"
        className={`customer-tab-btn ${activeTab === 'pesan' ? 'active' : ''}`}
        onClick={() => onChangeTab('pesan')}
      >
        <span className="tab-icon">📝</span>
        <span className="tab-text">Pesan</span>
      </button>

      <button
        type="button"
        className={`customer-tab-btn ${activeTab === 'riwayat' ? 'active' : ''}`}
        onClick={() => onChangeTab('riwayat')}
      >
        <div className="tab-icon-wrapper">
          <span className="tab-icon">📜</span>
          {hasActiveOrder && <span className="tab-badge-dot"></span>}
        </div>
        <span className="tab-text">Riwayat</span>
      </button>
    </nav>
  );
};

