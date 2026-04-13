$(function () {
  const getQuantity = ($button) => {
    const inputId = $button.attr("data-qty-input-id");
    const quantity = inputId ? parseInt($(`#${inputId}`).val(), 10) : parseInt($button.attr("data-quantity"), 10);

    return Number.isFinite(quantity) && quantity > 0 ? quantity : 1;
  };

  const hasKnownOrderSession = () => {
    return window.zippyHasOrderSession === true;
  };

  const getProductId = ($button) => {
    const prod_id = $button.data("prod");
    const product_id = $button.data("product_id") || $button.attr("data-product-id");

    return prod_id ? prod_id : product_id;
  };

  const triggerNativeAddToCart = (button) => {
    button.dataset.zippyNativeAddToCart = "1";
    button.click();
  };

  const setShippingPopupProduct = (productId, quantity = 1) => {
    const $form = $("#lightbox-zippy-form");

    if (!$form.length) {
      console.warn("Shipping popup form was not found.");
      return false;
    }

    $form.attr("data-product_id", productId);
    $form.attr("data-quantity", quantity);
    return true;
  };

  const getAddToCartUrl = ($button, productId, quantity) => {
    const productUrl = $button.attr("data-product-url") || window.location.href;
    let targetUrl = new URL(productUrl, window.location.origin);

    if (targetUrl.origin !== window.location.origin) {
      targetUrl = new URL(window.location.href);
    }

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

  document.addEventListener("click", function (event) {
    const button = event.target.closest(".lightbox-zippy-btn");
    if (!button) {
      return;
    }

    const $button = $(button);
    const productId = getProductId($button);

    if (!productId) {
      return;
    }

    const quantity = getQuantity($button);

    if (hasKnownOrderSession()) {
      event.preventDefault();
      event.stopPropagation();
      event.stopImmediatePropagation();
      switchToNativeAddToCart($button, productId, quantity);
      triggerNativeAddToCart(button);
      return;
    }

    if (!setShippingPopupProduct(productId, quantity)) {
      event.preventDefault();
    }
  }, true);

  document.addEventListener("click", function (event) {
    const button = event.target.closest(".add_to_cart_button");
    if (!button || button.closest(".lightbox-zippy-btn")) {
      return;
    }

    if (button.dataset.zippyNativeAddToCart === "1") {
      delete button.dataset.zippyNativeAddToCart;
      return;
    }

    const $button = $(button);
    const productId = $button.data("product_id") ||
      $button.attr("data-product_id") ||
      new URLSearchParams($button.attr("href")?.split("?")[1]).get("add-to-cart");

    if (!productId) {
      return;
    }

    const quantity = getQuantity($button);
    button.setAttribute("href", getAddToCartUrl($button, productId, quantity));
    button.setAttribute("data-quantity", String(quantity));
  }, true);

  $(document).on("click", ".btn-close-lightbox", function () {
    $(".mfp-close").trigger("click");
  });

});
