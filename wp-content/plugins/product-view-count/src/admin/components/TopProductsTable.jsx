import React from 'react'

const TopProductsTable = ({ products, loading }) => {
  if (loading) {
    return (
      <div className="flex items-center justify-center h-32">
        <div className="animate-spin rounded-full h-6 w-6 border-b-2 border-wp-blue"></div>
      </div>
    )
  }

  if (!products || products.length === 0) {
    return (
      <div className="text-center text-gray-500 py-8">
        No products found for the selected date range
      </div>
    )
  }

  const handleResetProduct = async (productId, productName) => {
    if (!confirm(`Are you sure you want to reset the view count for "${productName}"?`)) {
      return
    }

    try {
      const response = await fetch(`${window.pvcAdmin.apiUrl}/analytics/reset-product`, {
        method: 'POST',
        headers: {
          'X-WP-Nonce': window.pvcAdmin.nonce,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId })
      })

      if (!response.ok) throw new Error('Failed to reset product count')
      
      alert(`View count for "${productName}" has been reset successfully!`)
      window.location.reload() // Simple refresh for now
    } catch (error) {
      alert('Error: ' + error.message)
    }
  }

  return (
    <div className="overflow-x-auto">
      <table className="pvc-table">
        <thead>
          <tr>
            <th className="text-left">Rank</th>
            <th className="text-left">Product</th>
            <th className="text-right">Views</th>
            <th className="text-right">Unique Views</th>
            <th className="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          {products.map((product, index) => (
            <tr key={product.id}>
              <td className="font-medium">
                <span className={`inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold ${
                  index === 0 ? 'bg-yellow-100 text-yellow-800' :
                  index === 1 ? 'bg-gray-100 text-gray-800' :
                  index === 2 ? 'bg-orange-100 text-orange-800' :
                  'bg-blue-100 text-blue-800'
                }`}>
                  {index + 1}
                </span>
              </td>
              <td>
                <div className="flex items-center">
                  {product.image && (
                    <img 
                      src={product.image} 
                      alt={product.name}
                      className="w-10 h-10 rounded object-cover mr-3"
                    />
                  )}
                  <div>
                    <div className="font-medium text-gray-900">{product.name}</div>
                    {product.sku && (
                      <div className="text-sm text-gray-500">SKU: {product.sku}</div>
                    )}
                  </div>
                </div>
              </td>
              <td className="text-right font-medium">{product.total_views.toLocaleString()}</td>
              <td className="text-right">{product.unique_views.toLocaleString()}</td>
              <td className="text-center">
                <div className="flex items-center justify-center space-x-2">
                  <a
                    href={product.edit_url}
                    className="text-wp-blue hover:text-wp-blue-dark text-sm"
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    Edit
                  </a>
                  <span className="text-gray-300">|</span>
                  <a
                    href={product.view_url}
                    className="text-wp-blue hover:text-wp-blue-dark text-sm"
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    View
                  </a>
                  <span className="text-gray-300">|</span>
                  <button
                    onClick={() => handleResetProduct(product.id, product.name)}
                    className="text-red-600 hover:text-red-800 text-sm"
                  >
                    Reset
                  </button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}

export default TopProductsTable
