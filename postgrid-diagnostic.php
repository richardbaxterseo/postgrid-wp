<?php
/**
 * PostGrid Diagnostic Tool
 * 
 * Place this file in your WordPress root and access it directly
 * to diagnose PostGrid/Caxton block registration issues.
 */

// Load WordPress
require_once( 'wp-load.php' );

// Check if user can manage options
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'You need administrator privileges to run this diagnostic.' );
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>PostGrid Diagnostic</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; margin: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .warning { background: #fff3cd; color: #856404; }
        .info { background: #d1ecf1; color: #0c5460; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
        h2 { margin-top: 30px; }
    </style>
</head>
<body>
    <h1>PostGrid/Caxton Diagnostic Report</h1>
    
    <h2>Plugin Status</h2>
    <?php
    $plugin_file = 'caxton/postgrid.php';
    $is_active = is_plugin_active( $plugin_file );
    ?>
    <div class="status <?php echo $is_active ? 'success' : 'error'; ?>">
        Plugin Active: <?php echo $is_active ? 'Yes' : 'No'; ?>
    </div>
    
    <h2>Registered Blocks</h2>
    <?php
    $registry = WP_Block_Type_Registry::get_instance();
    $all_blocks = $registry->get_all_registered();
    $postgrid_blocks = array();
    $caxton_blocks = array();
    
    foreach ( $all_blocks as $block_name => $block ) {
        if ( strpos( $block_name, 'postgrid/' ) === 0 ) {
            $postgrid_blocks[] = $block_name;
        }
        if ( strpos( $block_name, 'caxton/' ) === 0 ) {
            $caxton_blocks[] = $block_name;
        }
    }
    ?>
    
    <h3>PostGrid Blocks (<?php echo count( $postgrid_blocks ); ?>)</h3>
    <?php if ( ! empty( $postgrid_blocks ) ) : ?>
        <ul>
            <?php foreach ( $postgrid_blocks as $block ) : ?>
                <li><?php echo esc_html( $block ); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <div class="status error">No PostGrid blocks registered!</div>
    <?php endif; ?>
    
    <h3>Caxton Blocks (<?php echo count( $caxton_blocks ); ?>)</h3>
    <?php if ( ! empty( $caxton_blocks ) ) : ?>
        <ul>
            <?php foreach ( $caxton_blocks as $block ) : ?>
                <li><?php echo esc_html( $block ); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <div class="status warning">No Caxton blocks registered!</div>
    <?php endif; ?>
    
    <h2>File System Check</h2>
    <?php
    $plugin_dir = WP_PLUGIN_DIR . '/caxton/';
    $checks = array(
        'Main plugin file' => $plugin_dir . 'postgrid.php',
        'Block.json' => $plugin_dir . 'block.json',
        'PostGrid class' => $plugin_dir . 'includes/class-postgrid.php',
        'Build directory' => $plugin_dir . 'build/',
        'Build index.js' => $plugin_dir . 'build/index.js',
        'Source directory' => $plugin_dir . 'src/',
        'Source index.js' => $plugin_dir . 'src/index.js',
    );
    ?>
    <table border="1" cellpadding="5" style="border-collapse: collapse;">
        <tr>
            <th>File/Directory</th>
            <th>Exists</th>
            <th>Path</th>
        </tr>
        <?php foreach ( $checks as $name => $path ) : ?>
            <tr>
                <td><?php echo esc_html( $name ); ?></td>
                <td class="<?php echo file_exists( $path ) ? 'success' : 'error'; ?>">
                    <?php echo file_exists( $path ) ? 'Yes' : 'No'; ?>
                </td>
                <td><code><?php echo esc_html( $path ); ?></code></td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>Block.json Content</h2>
    <?php
    $block_json_path = $plugin_dir . 'block.json';
    if ( file_exists( $block_json_path ) ) {
        $block_json = json_decode( file_get_contents( $block_json_path ), true );
        echo '<pre>' . esc_html( json_encode( $block_json, JSON_PRETTY_PRINT ) ) . '</pre>';
    } else {
        echo '<div class="status error">block.json not found!</div>';
    }
    ?>
    
    <h2>Script/Style Registration</h2>
    <?php
    global $wp_scripts, $wp_styles;
    
    $postgrid_scripts = array();
    $postgrid_styles = array();
    
    if ( isset( $wp_scripts->registered ) ) {
        foreach ( $wp_scripts->registered as $handle => $script ) {
            if ( strpos( $handle, 'postgrid' ) !== false || strpos( $script->src, 'caxton' ) !== false ) {
                $postgrid_scripts[ $handle ] = $script->src;
            }
        }
    }
    
    if ( isset( $wp_styles->registered ) ) {
        foreach ( $wp_styles->registered as $handle => $style ) {
            if ( strpos( $handle, 'postgrid' ) !== false || strpos( $style->src, 'caxton' ) !== false ) {
                $postgrid_styles[ $handle ] = $style->src;
            }
        }
    }
    ?>
    
    <h3>Registered Scripts</h3>
    <?php if ( ! empty( $postgrid_scripts ) ) : ?>
        <ul>
            <?php foreach ( $postgrid_scripts as $handle => $src ) : ?>
                <li><strong><?php echo esc_html( $handle ); ?>:</strong> <?php echo esc_html( $src ); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <div class="status warning">No PostGrid/Caxton scripts registered!</div>
    <?php endif; ?>
    
    <h3>Registered Styles</h3>
    <?php if ( ! empty( $postgrid_styles ) ) : ?>
        <ul>
            <?php foreach ( $postgrid_styles as $handle => $src ) : ?>
                <li><strong><?php echo esc_html( $handle ); ?>:</strong> <?php echo esc_html( $src ); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <div class="status warning">No PostGrid/Caxton styles registered!</div>
    <?php endif; ?>
    
    <h2>Recommendations</h2>
    <div class="status info">
        <h3>To fix the issues:</h3>
        <ol>
            <li><strong>Build the assets:</strong> Run <code>npm install && npm run build</code> in the plugin directory</li>
            <li><strong>Update block.json:</strong> Ensure it points to the build directory for scripts</li>
            <li><strong>Replace main files:</strong> Use the fixed versions created:
                <ul>
                    <li>postgrid-fixed.php → postgrid.php</li>
                    <li>block-fixed.json → block.json</li>
                    <li>class-postgrid-fixed.php → includes/class-postgrid.php</li>
                </ul>
            </li>
            <li><strong>Deactivate and reactivate</strong> the plugin</li>
            <li><strong>Clear caches</strong> if you're using any caching plugins</li>
        </ol>
    </div>
</body>
</html>
