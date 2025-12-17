# Product Traceability System

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

A comprehensive product traceability system built with Laravel 12 that tracks products from **supplier → inventory → production → transfer → sale** with full backward and forward traceability.

## 🎯 Features

- **Batch/Lot-based tracking**: Every product batch is tracked with unique batch numbers
- **Multiple locations**: Support for warehouses, shops, production facilities, suppliers, and customers
- **Production/Assembly tracking**: Optional production step that tracks raw materials used
- **Full traceability**: Trace backward to suppliers and forward to customers
- **Negative stock prevention**: Database constraints and service-level checks prevent negative inventory
- **RESTful API**: Complete API for all operations
- **Comprehensive documentation**: Full API documentation and Postman collection included

## 📋 Requirements

- PHP >= 8.2
- Laravel 12
- MySQL 5.7+ or MariaDB 10.3+
- Composer

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd meat-traceability
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Update `.env` file** with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed the database** (optional, for testing)
   ```bash
   php artisan db:seed
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000/api`

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api
```

### Response Format

All API responses follow a consistent format:

**Success Response:**
```json
{
  "success": true,
  "message": "Resource retrieved successfully",
  "data": { ... }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": { ... } // For validation errors
}
```

### Main Endpoints

#### Suppliers
- `GET /api/suppliers` - List suppliers
- `POST /api/suppliers` - Create supplier
- `GET /api/suppliers/{id}` - Get supplier
- `PUT /api/suppliers/{id}` - Update supplier
- `DELETE /api/suppliers/{id}` - Delete supplier

#### Products
- `GET /api/products` - List products
- `POST /api/products` - Create product
- `GET /api/products/{id}` - Get product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

#### Locations
- `GET /api/locations` - List locations
- `POST /api/locations` - Create location
- `GET /api/locations/{id}` - Get location
- `PUT /api/locations/{id}` - Update location
- `DELETE /api/locations/{id}` - Delete location

#### Batches
- `GET /api/batches` - List batches
- `POST /api/batches` - Create batch
- `GET /api/batches/{id}` - Get batch
- `PUT /api/batches/{id}` - Update batch
- `DELETE /api/batches/{id}` - Delete batch

#### Inventory Movements
- `GET /api/inventory-movements` - List movements
- `POST /api/inventory-movements` - Create movement (automatically updates stock)
- `GET /api/inventory-movements/{id}` - Get movement

#### Productions
- `GET /api/productions` - List productions
- `POST /api/productions` - Create production (consumes materials, creates output)
- `GET /api/productions/{id}` - Get production
- `PUT /api/productions/{id}` - Update production

#### Transfers
- `GET /api/transfers` - List transfers
- `POST /api/transfers` - Create transfer (reserves stock)
- `GET /api/transfers/{id}` - Get transfer
- `POST /api/transfers/{id}/complete` - Complete transfer (moves stock)

#### Sales
- `GET /api/sales` - List sales
- `POST /api/sales` - Create sale (reduces stock)
- `GET /api/sales/{id}` - Get sale

#### Traceability
- `GET /api/trace/backward/{batchId}` - Trace backward from batch
- `GET /api/trace/forward/{batchId}` - Trace forward from batch
- `GET /api/trace/full/{batchId}` - Full trace (both directions)
- `GET /api/trace/sale/{saleId}` - Trace from sale

## 🧪 Testing with Postman

### Import Postman Collection

1. Open Postman
2. Click **Import** button
3. Select the `postman_collection.json` file from the project root
4. The collection will be imported with all endpoints organized by resource

### Configure Environment Variable

The collection uses a `base_url` variable. To set it:

1. In Postman, click on the collection
2. Go to the **Variables** tab
3. Set `base_url` to your API base URL (default: `http://localhost:8000`)

Or create a Postman Environment:
- Create a new environment
- Add variable: `base_url` = `http://localhost:8000`
- Select the environment before making requests

### Example Workflow

1. **Create a Supplier**
   ```
   POST /api/suppliers
   ```

2. **Create a Product**
   ```
   POST /api/products
   ```

3. **Create a Location**
   ```
   POST /api/locations
   ```

4. **Create a Batch**
   ```
   POST /api/batches
   ```

5. **Add Stock (Inventory Movement)**
   ```
   POST /api/inventory-movements
   ```

6. **Create a Production** (optional)
   ```
   POST /api/productions
   ```

7. **Transfer Stock**
   ```
   POST /api/transfers
   POST /api/transfers/{id}/complete
   ```

8. **Create a Sale**
   ```
   POST /api/sales
   ```

9. **Trace a Batch**
   ```
   GET /api/trace/full/{batchId}
   ```

## 📖 Usage Examples

### Example: Customer Complaint Trace

When a customer complains about a product:

1. **Find the sale:**
   ```http
   GET /api/sales?customer_name=John Customer
   ```

2. **Trace from the sale:**
   ```http
   GET /api/trace/sale/{saleId}
   ```

This returns:
- The sale details
- The batch used
- Complete backward trace (supplier, raw materials, production history)
- Complete forward trace (all sales, transfers, current locations)

### Example: Create Production

```json
POST /api/productions
{
  "product_id": 4,
  "location_id": 3,
  "quantity": 500,
  "status": "completed",
  "production_date": "2024-02-05",
  "materials": [
    {
      "batch_id": 1,
      "quantity": 50
    }
  ]
}
```

This will:
1. Consume 50 units from batch 1 (raw material)
2. Create output batch (if provided)
3. Add 500 units to stock (output product)

## 🗄️ Database Schema

The system includes 10 main tables:

- `suppliers` - Supplier information
- `products` - Product master data
- `locations` - Physical locations
- `batches` - Product batches/lots
- `inventory_movements` - All stock movements
- `stock_levels` - Current stock levels
- `productions` - Production/assembly records
- `production_materials` - Raw materials used
- `transfers` - Stock transfers
- `sales` - Sales records

See `TRACEABILITY_SYSTEM.md` for detailed schema documentation.

## 🔒 Data Integrity

### Negative Stock Prevention

1. **Database Level**: Check constraints on `stock_levels` table
2. **Service Level**: `StockService` validates before updating stock
3. **Transaction Safety**: All stock operations use database transactions

### Scalability Considerations

- Key fields are indexed (batch_id, location_id, dates)
- Most tables use soft deletes for audit trail
- All list endpoints support pagination
- Relationships are loaded efficiently with eager loading

## 📝 Additional Documentation

For detailed documentation, see:
- `TRACEABILITY_SYSTEM.md` - Complete system documentation
- `postman_collection.json` - Postman collection with all endpoints

## 🛠️ Development

### Running Migrations
```bash
php artisan migrate
```

### Running Seeders
```bash
php artisan db:seed
```

### Running Specific Seeder
```bash
php artisan db:seed --class=SupplierSeeder
```

