import React, { useState, useEffect } from 'react';
import { productsAPI } from '../../services/api'; // Assuming admin methods will be added or use existing
// import AdminProductForm from './AdminProductForm'; // Component for Add/Edit Product

const AdminProductsPage = () => {
  const [products, setProducts] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState(null);
  // const [showProductForm, setShowProductForm] = useState(false);
  // const [editingProduct, setEditingProduct] = useState(null);

  const fetchProducts = async () => {
    setIsLoading(true);
    setError(null);
    try {
      // Using the public productsAPI.getAll for now.
      // An admin-specific endpoint might be needed for unpublished products, etc.
      const data = await productsAPI.getAll({ limit: 100 }); // Fetch more for admin view
      setProducts(data.products || data); // Adjust based on actual API response structure
    } catch (err) {
      setError(err.message || 'Failed to fetch products.');
      console.error("Fetch products error:", err);
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchProducts();
  }, []);

  const handleDeleteProduct = async (productId) => {
    if (window.confirm('Are you sure you want to delete this product?')) {
      try {
        // await adminAPI.deleteProduct(productId); // Needs to be added to services/api.js
        alert(`Mock delete product ID: ${productId}. Implement actual API call.`);
        // fetchProducts(); // Refresh list after delete
      } catch (err) {
        alert(`Failed to delete product: ${err.message}`);
      }
    }
  };

  // const handleEditProduct = (product) => {
  //   setEditingProduct(product);
  //   setShowProductForm(true);
  // };

  // const handleAddProduct = () => {
  //   setEditingProduct(null);
  //   setShowProductForm(true);
  // };

  // const handleFormClose = () => {
  //   setShowProductForm(false);
  //   setEditingProduct(null);
  //   fetchProducts(); // Refresh products after add/edit
  // };

  if (isLoading) return <p className="text-gray-700">Loading products...</p>;
  if (error) return <p className="text-red-600">Error: {error}</p>;

  return (
    <div>
      <div className="flex justify-between items-center mb-8">
        <h1 className="text-3xl font-bold text-gray-800">Manage Products</h1>
        <button
          // onClick={handleAddProduct}
          onClick={() => alert("Product form not yet implemented.")}
          className="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors font-semibold"
        >
          Add New Product
        </button>
      </div>

      {/* {showProductForm && (
        <AdminProductForm product={editingProduct} onClose={handleFormClose} />
      )} */}

      <div className="bg-white shadow-lg rounded-xl overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
              <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
              <th scope="col" className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {products && products.length > 0 ? products.map((product) => (
              <tr key={product.id} className="hover:bg-gray-50 transition-colors">
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm font-medium text-gray-900">{product.name}</div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm text-gray-600">{product.category?.name || 'N/A'}</div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <div className="text-sm text-gray-600">₦{product.price?.toLocaleString()}</div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${product.stock_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                    {product.stock_quantity}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button
                    // onClick={() => handleEditProduct(product)}
                    onClick={() => alert(`Edit product ID: ${product.id}. Form not yet implemented.`)}
                    className="text-indigo-600 hover:text-indigo-900 mr-4"
                  >
                    Edit
                  </button>
                  <button
                    onClick={() => handleDeleteProduct(product.id)}
                    className="text-red-600 hover:text-red-900"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            )) : (
              <tr>
                <td colSpan="5" className="px-6 py-12 text-center text-gray-500">
                  No products found.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default AdminProductsPage;
