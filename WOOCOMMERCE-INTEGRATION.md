# WooCommerce Integration - Hand and Vision Theme

## Overview

Your Hand and Vision WordPress theme now has a beautiful, fully-integrated WooCommerce shop! Products are displayed by categories on the shop page, and each artist can have their own dedicated products section.

---

## ✨ Features Implemented

### 1. **Custom Shop Page (archive-product.php)**

- Products organized by categories
- Beautiful grid layout (4 columns on desktop)
- Category descriptions and "View All" buttons
- Quick add-to-cart buttons on hover
- Sale and "Sold" badges
- Responsive design (mobile-friendly)
- Bilingual support (Hebrew/English)

### 2. **Artist-Product Relationship**

- Custom meta box in product admin to link products to artists
- Artists automatically display their products on their profile pages
- "More by [Artist Name]" sections on product pages
- Artist name displayed on product cards

### 3. **Enhanced Single Product Page (woocommerce/single-product.php)**

- Clean, modern layout
- Artist attribution with links
- Product gallery with zoom and lightbox
- Related products section
- Beautiful typography and spacing
- Mobile-optimized

### 4. **Professional Styling**

- Premium design matching your theme's aesthetic
- Smooth animations and hover effects
- Clear typography and spacing
- Accessible and user-friendly
- RTL support for Hebrew

---

## 🚀 Getting Started

### Step 1: Set Up Product Categories

1. Go to **Products → Categories** in WordPress admin
2. Create categories like:
   - Paintings
   - Sculptures
   - Prints
   - Ceramics
   - Photography
   - etc.
3. Add descriptions to categories (optional but recommended)

### Step 2: Add Products

1. Go to **Products → Add New**
2. Fill in:
   - Product name
   - Price
   - Short description (shows on product page top)
   - Full description (shows in "About This Piece" section)
   - Product images
3. In the right sidebar, select a **Product Category**
4. In the **Artist** meta box (right sidebar), select the artist who created this piece
5. Publish!

### Step 3: Link Products to Artists

- When editing a product, look for the "Artist" meta box in the right sidebar
- Select the artist from the dropdown menu
- Save the product
- The product will now appear on that artist's profile page automatically!

---

## 📋 How It Works

### Shop Page Display

The shop page automatically:

1. Groups products by their categories
2. Shows up to 8 products per category
3. Displays a "View All" button if there are more than 8 products in a category
4. Shows all products in a simple grid if no categories are assigned

### Artist Pages

Artist pages now automatically:

1. Query all products linked to that artist
2. Display them in a beautiful grid
3. Show "Add to Cart" buttons
4. Link to the full shop

### Product Pages

Each product page shows:

1. The artist who created it (with link to their profile)
2. Related products from the same artist
3. Standard WooCommerce features (gallery, cart, etc.)

---

## 🎨 Customization Tips

### Change Product Grid Columns

Edit `functions.php`, find the function:

```php
function handandvision_woocommerce_product_columns() {
    return 4; // Change this number (3 or 4 recommended)
}
```

### Change Products Per Page

Edit `functions.php`, find:

```php
function handandvision_products_per_page() {
    return 12; // Change this number
}
```

### Modify Shop Hero Text

Edit `archive-product.php` around lines 20-30 to change the hero text and subtitle.

### Customize Colors

Colors are defined in CSS variables in `hv-unified.css`:

- `--hv-primary`: Main color (green)
- `--hv-text`: Text color
- `--hv-text-light`: Light text color

---

## 📱 Responsive Behavior

- **Desktop (1200px+)**: 4 columns
- **Tablet (768px-1200px)**: 3 columns
- **Mobile (480px-768px)**: 2 columns
- **Small Mobile (<480px)**: 1 column

---

## 🌐 Bilingual Support

The theme automatically detects Hebrew/English using the `handandvision_is_hebrew()` function and displays appropriate text:

- **Hebrew**: "יצירות לרכישה", "הוסף לעגלה", etc.
- **English**: "Artworks For Sale", "Add to Cart", etc.

---

## 🔧 Technical Details

### Files Modified/Created:

1. **functions.php** - Added WooCommerce support and artist-product relationship
2. **archive-product.php** - Custom shop page template
3. **single-artist.php** - Enhanced with dynamic product section
4. **woocommerce/single-product.php** - Custom single product template
5. **assets/css/hv-unified.css** - Added comprehensive WooCommerce styles

### Key Functions Added:

- `handandvision_woocommerce_support()` - Declares WooCommerce support
- `handandvision_add_product_artist_metabox()` - Adds artist selector (up to 200 artists)
- `handandvision_save_product_artist()` - Saves artist relationship, invalidates cache
- `handandvision_get_artist_products()` - Retrieves products by artist (1h transient cache)
- `handandvision_invalidate_artist_products_cache()` - Clears cache when product artist changes
- `handandvision_woocommerce_product_columns()` - Sets grid columns
- `handandvision_products_per_page()` - Sets products per page

Artist products on artist pages come only from the product meta `_handandvision_artist_id` (no ACF fallback).

---

## 💡 Best Practices

### Product Images

- **Recommended size**: 600x800px (3:4 ratio)
- Use high-quality images
- Add multiple images for gallery view
- Use consistent style/lighting

### Pricing

- Use clear, consistent pricing
- ₪ symbol is supported automatically
- Sale prices will show with strikethrough

### Product Descriptions

- **Short description**: 2-3 sentences about the piece
- **Full description**: Detailed information about materials, dimensions, inspiration

### Categories

- Keep categories logical and organized
- Use descriptive category names
- Add category descriptions for SEO

---

## 🎯 Tips for Success

1. **High-Quality Photos**: Invest in good product photography
2. **Detailed Descriptions**: Help customers understand each piece
3. **Artist Attribution**: Always link products to artists
4. **Category Organization**: Use clear, intuitive categories
5. **Mobile Testing**: Check how products look on phones
6. **Regular Updates**: Add new products regularly

---

## 🐛 Troubleshooting

### Products not showing on artist page?

- Make sure you've selected the artist in the product's "Artist" meta box
- Check that products are published (not drafts)

### Shop page looks different?

- Clear your site cache
- Check if WooCommerce is active
- Ensure you're viewing the correct shop page

### Styling issues?

- Make sure both `hv-unified.css` and `hv-main.css` are in sync
- Clear browser cache
- Check for conflicting plugins

---

## 📞 Support

For questions or issues:

1. Check the WooCommerce documentation
2. Review this README
3. Contact your theme developer

---

**Enjoy your beautiful new online shop! 🎨🛍️**
