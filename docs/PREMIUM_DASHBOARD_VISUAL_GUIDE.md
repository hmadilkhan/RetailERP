# Premium Dashboard - Visual Guide

## 🎨 Layout Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                     PREMIUM DASHBOARD                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐       │
│  │ 💹       │  │ 💰       │  │ 📦       │  │ 🛒       │       │
│  │Projected │  │  Total   │  │  Total   │  │  Total   │       │
│  │  Sales   │  │  Sales   │  │ Products │  │  Orders  │       │
│  │  $1,234  │  │  $5,678  │  │   150    │  │    45    │       │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘       │
│                                                                  │
│  ┌────────────────────────────────┐  ┌──────────────────────┐  │
│  │                                │  │                      │  │
│  │   Terminal Daily Sales         │  │   Top 5 Products     │  │
│  │   (Bar Chart)                  │  │   (Doughnut Chart)   │  │
│  │                                │  │                      │  │
│  │   ████████                     │  │      ╱───╲          │  │
│  │   ████████                     │  │     ╱     ╲         │  │
│  │   ████████                     │  │    │   🍩  │        │  │
│  │   ████████                     │  │     ╲     ╱         │  │
│  │                                │  │      ╲───╱          │  │
│  └────────────────────────────────┘  └──────────────────────┘  │
│                                                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │                                                          │   │
│  │   Monthly Sales Chart (Livewire Component)              │   │
│  │   (Bar Chart - Multiple Branches)                       │   │
│  │                                                          │   │
│  │   ████ ████ ████ ████ ████ ████ ████ ████             │   │
│  │                                                          │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌────────────────────────────┐  ┌──────────────────────────┐  │
│  │                            │  │                          │  │
│  │  Yearly Sales Trend        │  │  Order Status Overview   │  │
│  │  (Line Chart)              │  │                          │  │
│  │                            │  │  Pending:      12        │  │
│  │      ╱╲    ╱╲              │  │  Processing:   8         │  │
│  │     ╱  ╲  ╱  ╲             │  │  Ready:        15        │  │
│  │    ╱    ╲╱    ╲            │  │  Delivered:    10        │  │
│  │                            │  │                          │  │
│  └────────────────────────────┘  └──────────────────────────┘  │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

## 📊 Component Breakdown

### 1. Stats Cards (Top Row)

```
┌─────────────────────────┐
│  💹 Icon (Gradient)     │
│                         │
│  Projected Sales        │
│  $1,234.56             │
│                         │
│  [Hover: Lifts up]     │
└─────────────────────────┘
```

**Features**:
- Gradient background on icon
- Hover lift animation
- Shadow effect
- Responsive sizing

**Colors**:
- Projected Sales: Blue gradient
- Total Sales: Green gradient
- Total Products: Info gradient
- Total Orders: Warning gradient

### 2. Terminal Daily Sales Chart

```
Terminal Sales (Bar Chart)
─────────────────────────────
Terminal 1  ████████ Cash
            ████ Card
            ██ Credit

Terminal 2  ██████ Cash
            ████████ Card
            ███ Credit
```

**Features**:
- Grouped bar chart
- Three payment types
- Interactive tooltips
- Legend at top
- Responsive height

**Colors**:
- Cash: Blue
- Credit Card: Red
- Customer Credit: Teal

### 3. Top 5 Products Chart

```
    Top Products (Doughnut)
    ─────────────────────
         ╱─────╲
        ╱   🍩  ╲
       │  Chart  │
        ╲       ╱
         ╲─────╱
    
    Legend:
    ● Product 1 (35%)
    ● Product 2 (25%)
    ● Product 3 (20%)
    ● Product 4 (12%)
    ● Product 5 (8%)
```

**Features**:
- Doughnut chart
- 5 color segments
- Percentage display
- Legend at bottom
- Interactive hover

**Colors**:
- 5 distinct colors
- High contrast
- Accessible palette

### 4. Monthly Sales Chart

```
Monthly Sales by Branch
────────────────────────────────
Jan  Feb  Mar  Apr  May  Jun
████ ████ ████ ████ ████ ████  Branch 1
████ ████ ████ ████ ████ ████  Branch 2
████ ████ ████ ████ ████ ████  Branch 3
```

**Features**:
- Reuses existing Livewire component
- Multiple branches
- Stacked bars
- 8-month history
- Color-coded branches

### 5. Yearly Sales Trend

```
Yearly Trend (Line Chart)
──────────────────────────
      ╱╲
     ╱  ╲    ╱╲
    ╱    ╲  ╱  ╲
   ╱      ╲╱    ╲
──────────────────────
2020 2021 2022 2023 2024
```

**Features**:
- Smooth line chart
- Filled area
- Gradient fill
- Year labels
- Trend visualization

**Colors**:
- Line: Blue
- Fill: Light blue gradient

### 6. Order Status Overview

```
┌──────────────────────┐
│  Order Status        │
├──────────────────────┤
│  ┌────────┬────────┐ │
│  │Pending │Process │ │
│  │   12   │   8    │ │
│  └────────┴────────┘ │
│  ┌────────┬────────┐ │
│  │ Ready  │Deliver │ │
│  │   15   │   10   │ │
│  └────────┴────────┘ │
└──────────────────────┘
```

**Features**:
- 4 status cards
- Color-coded
- Grid layout
- Light background

**Colors**:
- Pending: Warning (Yellow)
- Processing: Info (Blue)
- Ready: Primary (Blue)
- Delivered: Success (Green)

## 🎨 Color Palette

### Primary Colors
```
Blue:    #3498db (rgba(54, 162, 235, 0.8))
Red:     #e74c3c (rgba(255, 99, 132, 0.8))
Green:   #2ecc71 (rgba(75, 192, 192, 0.8))
Yellow:  #f39c12 (rgba(255, 206, 86, 0.8))
Purple:  #9b59b6 (rgba(153, 102, 255, 0.8))
```

### Gradient Backgrounds
```
Primary:  linear-gradient(135deg, #667eea 0%, #764ba2 100%)
Success:  linear-gradient(135deg, #f093fb 0%, #f5576c 100%)
Info:     linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)
Warning:  linear-gradient(135deg, #fa709a 0%, #fee140 100%)
```

## 🎭 Animations

### Hover Effects
```css
Card Hover:
  - Transform: translateY(-5px)
  - Shadow: Enhanced
  - Duration: 0.3s
  - Easing: ease

Icon Pulse:
  - Scale: 1.05
  - Duration: 0.2s
  - Easing: ease-in-out
```

### Loading States
```
Initial Load:
  - Fade in
  - Slide up
  - Duration: 0.5s

Chart Animation:
  - Progressive draw
  - Duration: 1s
  - Easing: ease-out
```

## 📱 Responsive Breakpoints

### Desktop (≥1200px)
```
┌─────────────────────────────────┐
│  [Card] [Card] [Card] [Card]    │
│  [Chart────────] [Chart──]      │
│  [Chart─────────────────]       │
│  [Chart────────] [Status]       │
└─────────────────────────────────┘
```

### Tablet (768px - 1199px)
```
┌──────────────────────┐
│  [Card] [Card]       │
│  [Card] [Card]       │
│  [Chart──────────]   │
│  [Chart──────────]   │
│  [Chart──────────]   │
│  [Status─────────]   │
└──────────────────────┘
```

### Mobile (<768px)
```
┌──────────┐
│  [Card]  │
│  [Card]  │
│  [Card]  │
│  [Card]  │
│  [Chart] │
│  [Chart] │
│  [Chart] │
│  [Status]│
└──────────┘
```

## 🎯 Interactive Elements

### Clickable Cards
```
Total Sales Card:
  - Cursor: pointer
  - Click: Navigate to sales-details
  - Hover: Lift effect
```

### Chart Interactions
```
All Charts:
  - Hover: Show tooltip
  - Click: Highlight segment
  - Legend: Toggle visibility
```

## 🌈 Visual Hierarchy

### Priority Levels
```
Level 1 (Most Important):
  - Stats cards
  - Large numbers
  - Primary metrics

Level 2 (Important):
  - Main charts
  - Terminal sales
  - Top products

Level 3 (Supporting):
  - Monthly trends
  - Yearly data
  - Order status
```

## 💡 Design Principles

### 1. Clarity
- Clear labels
- Readable fonts
- Sufficient spacing
- High contrast

### 2. Consistency
- Uniform card sizes
- Consistent spacing
- Standard colors
- Same icon style

### 3. Feedback
- Hover states
- Click feedback
- Loading indicators
- Error messages

### 4. Accessibility
- ARIA labels
- Keyboard navigation
- Screen reader support
- Color contrast

## 🎨 Typography

### Font Sizes
```
H1: 2.5rem (40px)  - Page title
H2: 2rem (32px)    - Section headers
H3: 1.75rem (28px) - Card titles
H4: 1.5rem (24px)  - Metrics
H5: 1.25rem (20px) - Labels
Body: 1rem (16px)  - Text
Small: 0.875rem    - Captions
```

### Font Weights
```
Bold:    700 - Numbers, titles
Semibold: 600 - Headers
Medium:   500 - Labels
Regular:  400 - Body text
```

## 🖼️ Spacing System

### Margins & Padding
```
xs:  0.25rem (4px)
sm:  0.5rem (8px)
md:  1rem (16px)
lg:  1.5rem (24px)
xl:  2rem (32px)
xxl: 3rem (48px)
```

### Grid Gaps
```
Cards:  1rem (16px)
Charts: 1.5rem (24px)
Sections: 2rem (32px)
```

## 🎪 Special Effects

### Card Shadow
```css
Default: 0 0.125rem 0.25rem rgba(0,0,0,0.075)
Hover:   0 0.5rem 1rem rgba(0,0,0,0.15)
```

### Border Radius
```css
Cards:   0.375rem (6px)
Buttons: 0.25rem (4px)
Icons:   50% (circle)
```

## 📐 Layout Grid

### Container
```
Max Width: 1320px
Padding: 1rem
Margin: auto
```

### Columns
```
Desktop: 12 columns
Tablet:  12 columns
Mobile:  12 columns (stacked)
```

---

**This visual guide helps you understand the premium dashboard's design and layout!**
