import React from 'react';
import './Header.css';

interface AppHeaderProps {
  title: string;
  subtitle?: string;
  rightAction?: React.ReactNode;
}

export const AppHeader: React.FC<AppHeaderProps> = ({ title, subtitle, rightAction }) => {
  return (
    <header className="app-header-container">
      <div className="app-header-titles">
        <h1 className="app-header-title">{title}</h1>
        {subtitle && <span className="app-header-subtitle">{subtitle}</span>}
      </div>
      {rightAction && <div className="app-header-right">{rightAction}</div>}
    </header>
  );
};
