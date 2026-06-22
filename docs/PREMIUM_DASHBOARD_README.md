# Premium Dashboard - Livewire 3 Component

## Overview
A modern, premium UI dashboard built with Livewire 3 for the Retail application. This dashboard provides real-time analytics and visualizations without disturbing the existing dashboard functionality.

## Features

### 📊 Visual Components
- **Stats Cards**: 4 animated cards showing key metrics
  - Projected Sales
  - All Closed Sales
  - Total Products
  - Total Orders

- **Charts & Graphs**:
  - Terminal Daily Sales (Bar Chart)
  - Top 5 Products (Doughnut Chart)
  - Monthly Sales Chart (Integrated from existing component)
  - Yearly Sales Trend (Line Chart)
  - Order Status Overview

### 🎨 Premium UI Features
- Modern card design with hover effects
- Gradient backgrounds
- Material Design Icons
- Responsive layout (Bootstrap 5)
- Smooth animations and transitions
- Chart.js integration for interactive charts

## File Structure

```
app/
└── Livewire/
    └── Dashboard/
        ├── PremiumDashboard.php          # Main Livewire component
        └── MonthlySalesChart.php          # Existing monthly chart component

resources/
└── views/
    └── livewire/
        └── dashboard/
            └── premium-dashboard.blade.php # Premium dashboard view

routes/
└── web.php                                 # Route definition
```

## Installation & Usage

### 1. Access the Premium Dashboard
Navigate to: `/premium-dashboard`

Or use the named route:
```php
route('premium.dashboard')
```

### 2. Route Definition
```php
Route::get('/premium-dashboard', \App\Livewire\Dashboard\PremiumDashboard::class)
    ->name('premium.dashboard');
```

### 3. Component Structure
```php
namespace App\Livewire\Dashboard;

use App\dashboard;
use Livewire\Component;

class PremiumDashboard extends Component
{
    public $products;
    public $totalstock;
    public $months;
    public $year;
    public $orders;
    public $branches;
    public $sales;
    public $totalSales;
    public $projected;
    public $permission;

    public function mount(dashboard $dash)
    {
        // Load all dashboard data
    }

    public function render()
    {
        return view('livewire.dashboard.premium-dashboard')
            ->layout('layouts.master-layout', [
                'title' => 'Premium Dashboard'
            ]);
    }
}
```

## Dependencies

### Required Libraries
- **Livewire 3**: Already installed in the project
- **Chart.js**: Loaded via CDN in the component
- **Bootstrap 5**: For responsive grid and utilities
- **Material Design Icons**: For premium icons

### CDN Resources Used
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

## Data Sources

The component uses the existing `dashboard` model methods:
- `getMostSalesProduct()` - Top 5 selling products
- `getTotalItems()` - Total product count
- `getMonthsSales()` - Monthly sales data
- `getYearlySales()` - Yearly sales data
- `orderStatus()` - Order statistics
- `branches()` - Branch information
- `sales()` - Terminal sales data
- `totalSales()` - Total closed sales
- `dashboardRole()` - Permission check
- `getProjectedSales()` - Sales projections

## Customization

### Changing Colors
Edit the chart colors in the blade file:
```javascript
backgroundColor: [
    'rgba(255, 99, 132, 0.8)',  // Red
    'rgba(54, 162, 235, 0.8)',  // Blue
    'rgba(255, 206, 86, 0.8)',  // Yellow
    // Add more colors as needed
]
```

### Modifying Card Styles
Update the CSS in the component:
```css
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
```

### Adding New Metrics
1. Add property to component:
```php
public $newMetric;
```

2. Load data in mount():
```php
$this->newMetric = $dash->getNewMetric();
```

3. Display in view:
```html
<div class="col-xl-3 col-md-6">
    <div class="card border-0 shadow-sm h-100 hover-lift">
        <!-- Your metric card -->
    </div>
</div>
```

## Testing the Component

### 1. Basic Test
Visit: `http://your-domain/premium-dashboard`

### 2. Check Permissions
The dashboard respects the existing permission system:
```php
@if ($permission)
    <!-- Dashboard content -->
@endif
```

### 3. Verify Data Loading
All data is loaded in the `mount()` method and should display automatically.

## Comparison with Original Dashboard

| Feature | Original Dashboard | Premium Dashboard |
|---------|-------------------|-------------------|
| UI Style | Classic | Modern/Premium |
| Charts | Morris.js | Chart.js |
| Layout | Traditional | Card-based |
| Animations | Minimal | Smooth transitions |
| Responsive | Yes | Enhanced |
| Route | `/dashboard` | `/premium-dashboard` |

## Browser Compatibility
- Chrome (Latest)
- Firefox (Latest)
- Safari (Latest)
- Edge (Latest)

## Performance Considerations
- Charts are rendered client-side
- Data is loaded once on mount
- No real-time updates (refresh page for new data)
- Optimized for fast initial load

## Future Enhancements
- [ ] Real-time data updates with Livewire polling
- [ ] Export charts as images
- [ ] Date range filters
- [ ] Branch-specific filtering
- [ ] Dark mode support
- [ ] Mobile app integration

## Troubleshooting

### Charts Not Displaying
1. Check browser console for errors
2. Verify Chart.js CDN is loading
3. Ensure data arrays are not empty

### Permission Issues
Check the `dashboardRole()` method returns proper permissions.

### Styling Issues
Ensure Bootstrap 5 CSS is loaded in the master layout.

## Support
For issues or questions, contact the development team or refer to:
- Laravel Documentation: https://laravel.com/docs
- Livewire Documentation: https://livewire.laravel.com
- Chart.js Documentation: https://www.chartjs.org

## License
This component is part of the Retail application and follows the same license terms.

---

**Created**: December 2024  
**Version**: 1.0.0  
**Framework**: Laravel + Livewire 3
