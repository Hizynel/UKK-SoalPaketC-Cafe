import React from 'react';
import './StatusBadge.css';

interface StatusBadgeProps {
  status: 'Baru' | 'Diproses' | 'Selesai' | 'Dibatalkan' | 'Tersedia' | 'Habis';
}

export const StatusBadge: React.FC<StatusBadgeProps> = ({ status }) => {
  const normalized = status.toLowerCase();
  return <span className={`status-badge-pill badge-${normalized}`}>{status}</span>;
};

