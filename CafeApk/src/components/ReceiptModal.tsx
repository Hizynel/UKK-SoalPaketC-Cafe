import React from 'react';
import { Order } from '../types';
import './ReceiptModal.css';

interface ReceiptModalProps {
  order: Order | null;
  onClose: () => void;
}

export const ReceiptModal: React.FC<ReceiptModalProps> = ({ order, onClose }) => {
  if (!order) return null;

  return (
    <div className="receipt-overlay" onClick={onClose}>
      <div className="receipt-paper" onClick={(e) => e.stopPropagation()}>
        <div className="receipt-header">
          <h2 className="receipt-shop-name">Kedai Kopi Senja</h2>
          <p className="receipt-subtitle">Kelola pesanan, stok bahan, dan laporan kafe</p>
          <div className="receipt-cut-line"></div>
        </div>

        <div className="receipt-meta">
          <div className="meta-line">
            <span>Kode Order:</span>
            <strong>{order.kode}</strong>
          </div>
          <div className="meta-line">
            <span>Pelanggan:</span>
            <span>{order.pelangganNama}</span>
          </div>
          <div className="meta-line">
            <span>Meja:</span>
            <span>{order.meja}</span>
          </div>
          <div className="meta-line">
            <span>Pembayaran:</span>
            <span>{order.metodePembayaran}</span>
          </div>
          <div className="meta-line">
            <span>Status:</span>
            <span className="font-semibold">{order.status}</span>
          </div>
        </div>

        <div className="receipt-cut-line"></div>

        <div className="receipt-items">
          {order.items.map((item, idx) => (
            <div key={idx} className="receipt-item-row">
              <span className="receipt-item-name">
                {item.qty}x {item.nama}
              </span>
              <span className="receipt-item-price">
                Rp {(item.qty * item.harga).toLocaleString('id-ID')}
              </span>
            </div>
          ))}
        </div>

        <div className="receipt-cut-line"></div>

        <div className="receipt-calc">
          <div className="calc-line">
            <span>Subtotal</span>
            <span>Rp {order.subtotal.toLocaleString('id-ID')}</span>
          </div>
          <div className="calc-line">
            <span>Service (5%)</span>
            <span>Rp {order.serviceCharge.toLocaleString('id-ID')}</span>
          </div>
          <div className="calc-line">
            <span>Pajak (10%)</span>
            <span>Rp {order.pajak.toLocaleString('id-ID')}</span>
          </div>
          <div className="calc-total">
            <span>TOTAL</span>
            <span>Rp {order.total.toLocaleString('id-ID')}</span>
          </div>
        </div>

        <div className="receipt-footer">
          <p>Terima kasih telah berkunjung!</p>
          <p className="footer-small">Struk Resmi Kedai Kopi Senja</p>
          <button type="button" className="receipt-close-btn" onClick={onClose}>
            Tutup
          </button>
        </div>
      </div>
    </div>
  );
};

