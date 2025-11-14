import React, { useState, useEffect } from 'react'
import Dashboard from './components/Dashboard'
import DateRangeFilter from './components/DateRangeFilter'
import { subDays, format } from 'date-fns'

function App() {
  const [dateRange, setDateRange] = useState({
    startDate: subDays(new Date(), 30),
    endDate: new Date()
  })
  
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)

  const handleDateRangeChange = (newRange) => {
    setDateRange(newRange)
  }

  return (
    <div className="pvc-admin-dashboard p-6">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900 mb-2">
          Product View Analytics
        </h1>
        <p className="text-gray-600">
          Track and analyze your WooCommerce product views with advanced insights
        </p>
      </div>

      <div className="mb-6">
        <DateRangeFilter 
          dateRange={dateRange}
          onChange={handleDateRangeChange}
          loading={loading}
        />
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
          <strong>Error:</strong> {error}
        </div>
      )}

      <Dashboard 
        dateRange={dateRange}
        loading={loading}
        setLoading={setLoading}
        setError={setError}
      />
    </div>
  )
}

export default App
