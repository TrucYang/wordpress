import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'
import './index.css'

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
  const container = document.getElementById('pvc-admin-dashboard')
  if (container) {
    const root = ReactDOM.createRoot(container)
    root.render(<App />)
  }
})
