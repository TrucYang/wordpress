import React from 'react'

const StatsCards = ({ stats, loading }) => {
  const cards = [
    {
      title: 'Total Views',
      value: stats.totalViews,
      icon: '👁️',
      color: 'blue'
    },
    {
      title: 'Unique Visitors',
      value: stats.uniqueVisitors,
      icon: '👥',
      color: 'green'
    },
    {
      title: 'Avg Views/Product',
      value: stats.avgViewsPerProduct,
      icon: '📊',
      color: 'purple',
      decimal: true
    },
    {
      title: 'Top Product Views',
      value: stats.topViewedProduct?.views || 0,
      subtitle: stats.topViewedProduct?.name || 'No data',
      icon: '🏆',
      color: 'yellow'
    }
  ]

  const getColorClasses = (color) => {
    const colors = {
      blue: 'bg-blue-50 border-blue-200 text-blue-800',
      green: 'bg-green-50 border-green-200 text-green-800',
      purple: 'bg-purple-50 border-purple-200 text-purple-800',
      yellow: 'bg-yellow-50 border-yellow-200 text-yellow-800'
    }
    return colors[color] || colors.blue
  }

  if (loading) {
    return (
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {[1, 2, 3, 4].map((i) => (
          <div key={i} className="pvc-card animate-pulse">
            <div className="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
            <div className="h-8 bg-gray-200 rounded w-1/2"></div>
          </div>
        ))}
      </div>
    )
  }

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      {cards.map((card, index) => (
        <div key={index} className={`pvc-card border-l-4 ${getColorClasses(card.color)}`}>
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium opacity-75">{card.title}</p>
              <p className="text-2xl font-bold">
                {card.decimal 
                  ? parseFloat(card.value).toFixed(1)
                  : card.value.toLocaleString()
                }
              </p>
              {card.subtitle && (
                <p className="text-xs opacity-60 mt-1 truncate" title={card.subtitle}>
                  {card.subtitle}
                </p>
              )}
            </div>
            <div className="text-2xl opacity-75">
              {card.icon}
            </div>
          </div>
        </div>
      ))}
    </div>
  )
}

export default StatsCards
