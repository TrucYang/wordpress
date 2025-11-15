import React, { useState, useEffect } from 'react'
import ViewsChart from './ViewsChart'
import TopProductsTable from './TopProductsTable'
import StatsCards from './StatsCards'
import { format } from 'date-fns'

const Dashboard = ({ dateRange, loading, setLoading, setError }) => {
  const [chartData, setChartData] = useState(null)
  const [topProducts, setTopProducts] = useState([])
  const [stats, setStats] = useState({
    totalViews: 0,
    uniqueVisitors: 0,
    avgViewsPerProduct: 0,
    topViewedProduct: null
  })

  useEffect(() => {
    fetchAnalyticsData()
  }, [dateRange])

  const fetchAnalyticsData = async () => {
    setLoading(true)
    setError(null)

    try {
      const startDate = format(dateRange.startDate, 'yyyy-MM-dd')
      const endDate = format(dateRange.endDate, 'yyyy-MM-dd')

      // Fetch chart data
      const chartResponse = await fetch(
        `${window.pvcAdmin.apiUrl}/analytics/chart?start_date=${startDate}&end_date=${endDate}`,
        {
          headers: {
            'X-WP-Nonce': window.pvcAdmin.nonce
          }
        }
      )

      if (!chartResponse.ok) throw new Error('Failed to fetch chart data')
      const chartResult = await chartResponse.json()
      setChartData(chartResult)

      // Fetch top products
      const productsResponse = await fetch(
        `${window.pvcAdmin.apiUrl}/analytics/top-products?start_date=${startDate}&end_date=${endDate}&limit=10`,
        {
          headers: {
            'X-WP-Nonce': window.pvcAdmin.nonce
          }
        }
      )

      if (!productsResponse.ok) throw new Error('Failed to fetch top products')
      const productsResult = await productsResponse.json()
      setTopProducts(productsResult)

      // Fetch stats
      const statsResponse = await fetch(
        `${window.pvcAdmin.apiUrl}/analytics/stats?start_date=${startDate}&end_date=${endDate}`,
        {
          headers: {
            'X-WP-Nonce': window.pvcAdmin.nonce
          }
        }
      )

      if (!statsResponse.ok) throw new Error('Failed to fetch stats')
      const statsResult = await statsResponse.json()
      setStats(statsResult)

    } catch (error) {
      console.error('Analytics fetch error:', error)
      setError(error.message)
    } finally {
      setLoading(false)
    }
  }

  const handleResetAllCounts = async () => {
    if (!confirm('Are you sure you want to reset all product view counts? This action cannot be undone.')) {
      return
    }

    try {
      const response = await fetch(`${window.pvcAdmin.apiUrl}/analytics/reset-all`, {
        method: 'POST',
        headers: {
          'X-WP-Nonce': window.pvcAdmin.nonce,
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) throw new Error('Failed to reset counts')
      
      alert('All view counts have been reset successfully!')
      fetchAnalyticsData() // Refresh data
    } catch (error) {
      setError(error.message)
    }
  }

  return (
    <div className="space-y-6">
      <StatsCards stats={stats} loading={loading} />
      
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="pvc-card">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-xl font-semibold text-gray-900">View Trends</h2>
            <button
              onClick={handleResetAllCounts}
              className="pvc-button-secondary text-sm"
              disabled={loading}
            >
              Reset All Counts
            </button>
          </div>
          <ViewsChart data={chartData} loading={loading} />
        </div>

        <div className="pvc-card">
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Top Viewed Products</h2>
          <TopProductsTable products={topProducts} loading={loading} />
        </div>
      </div>
    </div>
  )
}

export default Dashboard
