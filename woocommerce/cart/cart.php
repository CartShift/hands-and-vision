<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * @package HandAndVision
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

$is_hebrew = handandvision_is_hebrew();
?>

<div class="hv-cart-page">
    <div class="hv-container">

        <!-- Cart Header -->
        <div class="hv-cart-header">
            <h1 class="hv-headline-2"><?php echo esc_html( $is_hebrew ? 'סל הקניות שלך' : 'Your Shopping Cart' ); ?></h1>
            <?php if ( ! WC()->cart->is_empty() ) : ?>
                <p class="hv-cart-count">
                    <?php
                    $item_count = WC()->cart->get_cart_contents_count();
                    echo esc_html(
                        $is_hebrew
                            ? sprintf( '%d פריטים', $item_count )
                            : sprintf( _n( '%d item', '%d items', $item_count, 'woocommerce' ), $item_count )
                    );
                    ?>
                </p>
            <?php endif; ?>
        </div>

        <?php do_action( 'woocommerce_before_cart' ); ?>

    <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
        <?php do_action( 'woocommerce_before_cart_table' ); ?>

        <div class="hv-cart-table-wrapper">
            <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                <thead>
                    <tr>
                        <th class="product-thumbnail" scope="col">
                            <?php echo esc_html( $is_hebrew ? 'תמונה' : 'Image' ); ?>
                        </th>
                        <th class="product-name" scope="col">
                            <?php echo esc_html( $is_hebrew ? 'מוצר' : 'Product' ); ?>
                        </th>
                        <th class="product-price" scope="col">
                            <?php echo esc_html( $is_hebrew ? 'מחיר' : 'Price' ); ?>
                        </th>
                        <th class="product-quantity" scope="col">
                            <?php echo esc_html( $is_hebrew ? 'כמות' : 'Quantity' ); ?>
                        </th>
                        <th class="product-subtotal" scope="col">
                            <?php echo esc_html( $is_hebrew ? 'סה״כ' : 'Subtotal' ); ?>
                        </th>
                        <th class="product-remove" scope="col">
                            <span class="screen-reader-text"><?php echo esc_html( $is_hebrew ? 'הסר' : 'Remove' ); ?></span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php do_action( 'woocommerce_before_cart_contents' ); ?>

                    <?php
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                            ?>
                            <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                                <!-- Product Thumbnail -->
                                <td class="product-thumbnail">
                                    <?php
                                    $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                                    if ( ! $product_permalink ) {
                                        echo wp_kses_post( $thumbnail );
                                    } else {
                                        printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) );
                                    }
                                    ?>
                                </td>

                                <!-- Product Name -->
                                <td class="product-name" data-title="<?php echo esc_html( $is_hebrew ? 'מוצר' : 'Product' ); ?>">
                                    <div class="hv-cart-item__info">
                                        <?php
                                        if ( ! $product_permalink ) {
                                            echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                                        } else {
                                            echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                                        }

                                        do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

                                        // Meta data.
                                        echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

                                        // Backorder notification.
                                        if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                                            echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
                                        }
                                        ?>
                                    </div>
                                </td>

                                <!-- Product Price -->
                                <td class="product-price" data-title="<?php echo esc_attr( $is_hebrew ? 'מחיר' : 'Price' ); ?>">
                                    <?php
                                        echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    ?>
                                </td>

                                <!-- Product Quantity -->
                                <td class="product-quantity" data-title="<?php echo esc_html( $is_hebrew ? 'כמות' : 'Quantity' ); ?>">
                                    <?php
                                    if ( $_product->is_sold_individually() ) {
                                        $min_quantity = 1;
                                        $max_quantity = 1;
                                    } else {
                                        $min_quantity = 0;
                                        $max_quantity = $_product->get_max_purchase_quantity();
                                    }

                                    $product_quantity = woocommerce_quantity_input(
                                        [
                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                            'input_value'  => $cart_item['quantity'],
                                            'max_value'    => $max_quantity,
                                            'min_value'    => $min_quantity,
                                            'product_name' => $_product->get_name(),
                                        ],
                                        $_product,
                                        false
                                    );

                                    echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    ?>
                                </td>

                                <!-- Product Subtotal -->
                                <td class="product-subtotal" data-title="<?php echo esc_html( $is_hebrew ? 'סה״כ' : 'Subtotal' ); ?>">
                                    <?php
                                        echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    ?>
                                </td>

                                <!-- Product Remove -->
                                <td class="product-remove">
                                    <?php
                                        echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                            'woocommerce_cart_item_remove_link',
                                            sprintf(
                                                '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s" data-cart_item_key="%s"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></a>',
                                                esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                                /* translators: %s is the product name */
                                                esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $_product->get_name() ) ) ),
                                                esc_attr( $product_id ),
                                                esc_attr( $_product->get_sku() ),
                                                esc_attr( $cart_item_key )
                                            ),
                                            $cart_item_key
                                        );
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>

                    <?php do_action( 'woocommerce_cart_contents' ); ?>

                    <tr>
                        <td colspan="6" class="actions">
                            <div class="hv-cart-actions">
                                <?php if ( wc_coupons_enabled() ) { ?>
                                    <div class="coupon hv-coupon-wrapper">
                                        <label for="coupon_code" class="screen-reader-text"><?php echo esc_html( $is_hebrew ? 'קופון:' : __( 'Coupon:', 'woocommerce' ) ); ?></label>
                                        <input type="text" name="coupon_code" class="input-text hv-input" id="coupon_code" value="" placeholder="<?php echo esc_attr( $is_hebrew ? 'קוד קופון' : __( 'Coupon code', 'woocommerce' ) ); ?>" />
                                        <button type="submit" class="hv-btn hv-btn--outline hv-btn--small" name="apply_coupon" value="<?php echo esc_attr( $is_hebrew ? 'החל' : __( 'Apply coupon', 'woocommerce' ) ); ?>">
                                            <?php echo esc_html( $is_hebrew ? 'החל קופון' : __( 'Apply coupon', 'woocommerce' ) ); ?>
                                        </button>
                                        <?php do_action( 'woocommerce_cart_coupon' ); ?>
                                    </div>
                                <?php } ?>

                                <button type="submit" class="hv-btn hv-btn--outline" name="update_cart" value="<?php echo esc_attr( $is_hebrew ? 'עדכן' : __( 'Update cart', 'woocommerce' ) ); ?>">
                                    <?php echo esc_html( $is_hebrew ? 'עדכן סל' : __( 'Update cart', 'woocommerce' ) ); ?>
                                </button>

                                <?php do_action( 'woocommerce_cart_actions' ); ?>

                                <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                            </div>
                        </td>
                    </tr>

                    <?php do_action( 'woocommerce_after_cart_contents' ); ?>
                </tbody>
            </table>
        </div>

        <?php do_action( 'woocommerce_after_cart_table' ); ?>
    </form>

    <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

    <div class="cart-collaterals">
        <?php
            /**
             * Cart collaterals hook.
             *
             * @hooked woocommerce_cross_sell_display
             * @hooked woocommerce_cart_totals - 10
             */
            do_action( 'woocommerce_cart_collaterals' );
        ?>
    </div>

    <?php do_action( 'woocommerce_after_cart' ); ?>

    </div><!-- .hv-container -->
</div><!-- .hv-cart-page -->
