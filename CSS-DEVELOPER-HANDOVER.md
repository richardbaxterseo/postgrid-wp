# PostGrid CSS Development Handover

## Project Overview
**Plugin**: PostGrid WordPress Block Plugin  
**Version**: 0.1.10  
**Purpose**: Display posts in a responsive grid layout with customizable styling  
**Key Requirement**: Mobile-first, responsive design without any framework dependencies

## Current CSS Architecture

### File Structure
```
postgrid/
├── src/
│   └── style.css         # Source styles (development)
├── build/
│   └── style-index.css   # Compiled styles (production)
└── block.json            # Block configuration
```

### Build Process
- **npm run build**: Compiles CSS from src/ to build/
- **npm run start**: Watches for changes during development
- CSS is automatically prefixed and minified

## CSS Class Structure

### Main Container
```css
.wp-block-postgrid {
    /* Main grid container */
    /* Uses CSS Grid for layout */
    /* Responsive by default with auto-fill */
}
```

### Column Modifiers
```css
.wp-block-postgrid.columns-1  /* Single column layout */
.wp-block-postgrid.columns-2  /* Two columns */
.wp-block-postgrid.columns-3  /* Three columns */
.wp-block-postgrid.columns-4  /* Four columns */
```

### Individual Post Item
```css
.wp-block-postgrid__item {
    /* Each post card/item */
    /* Uses flexbox for internal layout */
    /* Has hover effects */
}
```

### Component Classes
```css
.wp-block-postgrid__thumbnail    /* Featured image container */
.wp-block-postgrid__title        /* Post title */
.wp-block-postgrid__meta         /* Meta information wrapper */
.wp-block-postgrid__author       /* Author name */
.wp-block-postgrid__date         /* Post date */
.wp-block-postgrid__categories   /* Category list */
.wp-block-postgrid__excerpt      /* Post excerpt */
.wp-block-postgrid__no-posts     /* Empty state message */
```

### Utility Classes
```css
.wp-block-postgrid--loading      /* Loading state */
.wp-block-postgrid--animated     /* Animation enabled */
.wp-block-postgrid--no-gap       /* Remove grid gap */
.wp-block-postgrid--large-gap    /* Double grid gap */
.wp-block-postgrid--rounded      /* Extra rounded corners */
.wp-block-postgrid--flat         /* Flat design (no shadows) */
```

## CSS Custom Properties (Design Tokens)

The design uses CSS custom properties for easy theming:

```css
:root {
    --postgrid-gap: 1.5rem;                    /* Grid gap */
    --postgrid-item-bg: #f5f5f5;              /* Card background */
    --postgrid-item-padding: 1.5rem;          /* Card padding */
    --postgrid-item-radius: 4px;              /* Border radius */
    --postgrid-item-shadow: 0 2px 4px rgba(); /* Box shadow */
    --postgrid-item-shadow-hover: 0 4px 8px;  /* Hover shadow */
    --postgrid-title-color: #1e1e1e;          /* Title color */
    --postgrid-title-size: 1.25rem;           /* Title size */
    --postgrid-meta-color: #666;              /* Meta text color */
    --postgrid-meta-size: 0.875rem;           /* Meta text size */
    --postgrid-excerpt-color: #333;           /* Excerpt color */
    --postgrid-excerpt-size: 0.9375rem;       /* Excerpt size */
    --postgrid-transition: all 0.3s ease;     /* Transition timing */
}
```

## Responsive Design Requirements

### Current Breakpoints
```css
/* Desktop: Full columns as specified */
/* Default */

/* Laptop/Tablet Landscape */
@media (max-width: 1200px) {
    /* 4 columns become more flexible */
}

/* Tablet Portrait */
@media (max-width: 992px) {
    /* 3-4 columns become 2 */
}

/* Mobile */
@media (max-width: 768px) {
    /* All layouts become single column */
}
```

### Mobile-First Approach
- Start with mobile layout
- Progressive enhancement for larger screens
- Touch-friendly tap targets (min 44px)
- Readable font sizes without zooming

## Key Styling Considerations

### 1. Grid System
- Uses CSS Grid with `auto-fill` and `minmax()`
- Automatically responsive without media queries
- Gap is consistent using custom property

### 2. Card Design
- Flexible height to accommodate content
- Consistent padding and shadows
- Hover effects for desktop (transform + shadow)
- Focus states for accessibility

### 3. Typography
- System font stack for performance
- Hierarchical sizing (title > meta > excerpt)
- Adequate line-height for readability
- Contrast ratios meet WCAG AA standards

### 4. Images
- Responsive images with `width: 100%`
- Maintain aspect ratio
- Subtle scale effect on hover
- Graceful handling when no image exists

### 5. Dark Mode Support
- Already includes `prefers-color-scheme` media query
- Adjusts colors automatically
- Maintains contrast ratios

## Development Tasks

### Immediate Priorities
1. **Review responsive behaviour** on real devices
2. **Enhance mobile tap targets** - ensure 44px minimum
3. **Add loading skeleton** styles for better UX
4. **Improve focus indicators** for keyboard navigation

### Enhancement Opportunities
1. **Animation polish**
   - Stagger animations for grid items
   - Smooth transitions between layouts
   - Loading placeholders

2. **Theme integration**
   - Inherit more from WordPress theme
   - Support for custom color schemes
   - Better integration with block editor

3. **Advanced layouts**
   - Masonry option
   - Featured post (larger first item)
   - List view option

4. **Performance**
   - Critical CSS extraction
   - Lazy loading for images
   - Reduced motion preferences

## Testing Requirements

### Browser Support
- Chrome/Edge (latest 2 versions)
- Firefox (latest 2 versions)
- Safari (latest 2 versions)
- Mobile Safari (iOS 12+)
- Chrome Mobile (Android 8+)

### Device Testing
- **Mobile**: 320px - 768px
- **Tablet**: 768px - 1024px
- **Desktop**: 1024px+
- **Large screens**: 1920px+

### Accessibility
- Keyboard navigation
- Screen reader testing
- Color contrast (WCAG AA)
- Focus indicators
- Reduced motion support

## Development Environment

### Setup
```bash
# Install dependencies
npm install

# Start development
npm run start

# Build for production
npm run build
```

### File Locations
- **Edit**: `src/style.css`
- **Test**: Load block in editor/frontend
- **Build**: Automatic via npm scripts

### WordPress Integration
- Styles are automatically enqueued
- Editor styles match frontend
- Block supports wide/full alignment

## Common Customization Requests

1. **Change grid gap**: Modify `--postgrid-gap`
2. **Adjust card styling**: Update item properties
3. **Custom breakpoints**: Add media queries
4. **Theme matching**: Use theme's CSS variables
5. **Remove animations**: Delete transition/transform rules

## Code Standards

1. **BEM methodology** for class names
2. **Mobile-first** media queries
3. **CSS custom properties** for theming
4. **Semantic HTML** structure
5. **No !important** unless absolutely necessary
6. **Comments** for complex sections

## Contact & Resources

- **GitHub**: https://github.com/richardbaxterseo/postgrid-wp
- **WordPress Block Editor Handbook**: https://developer.wordpress.org/block-editor/
- **Current CSS**: Check `src/style.css` for latest code

## Notes for Development

1. The plugin uses native CSS Grid, not flexbox for the main layout
2. All animations should respect `prefers-reduced-motion`
3. The block editor preview should match frontend exactly
4. Consider WordPress theme styles that might conflict
5. Test with various content lengths (short/long titles, with/without images)

The CSS is intentionally minimal and framework-free to ensure compatibility and performance. Focus on enhancing the responsive experience while maintaining the clean, modern aesthetic.
