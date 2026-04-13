$(function () {
  const getQuantity = ($button) => {
    const inputId = $button.attr("data-qty-input-id");
    const quantity = inputId ? parseInt($(`#${inputId}`).val(), 10) : parseInt($button.attr("data-quantity"), 10);

    return Number.isFinite(quantity) && quantity > 0 ? quantity : 1;
  };

  const hasOrderSession = (sessionData) => {
    return Boolean(sessionData?.order_mode);
  };

  const getProductId = ($button) => {
    const prod_id = $button.data("prod");
    const product_id = $button.data("product_id") || $button.attr("data-product-id");

    return prod_id ? prod_id : product_id;
  };

  const fetchCartSession = () => {
    return $.ajax({
      url: "/wp-json/zippy-addons/v1/get-cart-session",
      type: "GET",
      xhrFields: {
        withCredentials: true,
      },
    });
  };

  const openShippingPopup = (productId, quantity = 1) => {
    const $form = $("#lightbox-zippy-form");

    $form.attr("data-product_id", "");
    $form.attr("data-quantity", "");

    setTimeout(() => {
      $form.attr("data-product_id", productId);
      $form.attr("data-quantity", quantity);

      if ($.magnificPopup) {
        $.magnificPopup.open({
          items: {
            src: "#lightbox-zippy-form",
          },
          type: "inline",
        });
      }
    }, 10);
  };

  const getAddToCartUrl = ($button, productId, quantity) => {
    const productUrl = $button.attr("data-product-url") || window.location.href;
    const targetUrl = new URL(productUrl, window.location.origin);

    if (!targetUrl.searchParams.get("add-to-cart")) {
      targetUrl.searchParams.set("add-to-cart", productId);
    }

    targetUrl.searchParams.set("quantity", String(quantity));
    return targetUrl.toString();
  };

  const switchToNativeAddToCart = ($button, productId, quantity) => {
    const wooButtonClasses = $button.attr("data-woo-button-classes") || "add_to_cart_button ajax_add_to_cart";

    $button
      .removeClass("lightbox-zippy-btn zippy-session-add-cart")
      .addClass(wooButtonClasses)
      .attr("href", getAddToCartUrl($button, productId, quantity))
      .attr("data-add-cart", "")
      .attr("data-quantity", quantity);
  };

  document.addEventListener(
    "click",
    function (event) {
      const button = event.target.closest(".zippy-home-add-cart.add_to_cart_button");
      if (!button) {
        return;
      }

      const $button = $(button);
      const productId = getProductId($button);
      if (!productId) {
        return;
      }

      const quantity = getQuantity($button);
      button.setAttribute("href", getAddToCartUrl($button, productId, quantity));
      button.setAttribute("data-quantity", String(quantity));
    },
    true
  );

  document.addEventListener("click", async function (event) {
    const button = event.target.closest(".lightbox-zippy-btn");
    if (!button) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();

    const $button = $(button);
    const final_id = getProductId($button);
    
    if (!final_id || $button.hasClass("is-loading")) {
      return;
    }

    $button.addClass("is-loading");

    try {
      const quantity = getQuantity($button);
      const sessionResponse = await fetchCartSession();
      const sessionData = sessionResponse?.data || {};

      if (hasOrderSession(sessionData)) {
        switchToNativeAddToCart($button, final_id, quantity);
        $button[0].click();
        return;
      }

      openShippingPopup(final_id, quantity);
    } catch (error) {
      console.error("Cart session check failed:", error);
      openShippingPopup(final_id, getQuantity($button));
    } finally {
      $button.removeClass("is-loading");
    }
  }, true);

  $(document).on("click", ".btn-close-lightbox", function () {
    $(".mfp-close").trigger("click");
  });

});
