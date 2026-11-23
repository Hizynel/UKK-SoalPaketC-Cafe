import React from 'react';
import './PriceSummary.css';

interface PriceSummaryProps {
  subtotal: number;
  serviceCharge: number;
  pajak: number;
  total: number;
}

export const PriceSummary: React.FC<PriceSummaryProps> = ({
  subtotal,
  serviceCharge,
  pajak,
  total,
}) => {
  return (
    <div className="price-summary-container">
      <div className="price-summary-row">
        <span>Subtotal</span>
        <span>Rp {subtotal.toLocaleString('id-ID')}</span>
      </div>
      <div className="price-summary-row">
        <span>Service 5%</span>
        <span>Rp {serviceCharge.toLocaleString('id-ID')}</span>
      </div>
      <div className="price-summary-row">
        <span>Pajak 10%</span>
        <span>Rp {pajak.toLocaleString('id-ID')}</span>
      </div>
      <div className="price-summary-divider"></div>
      <div className="price-summary-row price-summary-total">
        <span>Total</span>
        <span>Rp {total.toLocaleString('id-ID')}</span>
      </div>
    </div>
  );
};

