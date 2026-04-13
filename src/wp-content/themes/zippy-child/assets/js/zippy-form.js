$(function () {
  const apiToken = "FEhI30q7ySHtMfzvSDo6RkxZUDVaQ1BBU3lBcGhYS3BrQStIUT09";

  const getApiHeaders = () => {
    const headers = {
      Authorization: `Bearer ${apiToken}`,
    };

    if (typeof admin_data !== "undefined" && admin_data.nonce) {
      headers["X-WP-Nonce"] = admin_data.nonce;
    }

    return headers;
  };

  const getQuantity = ($button) => {
    const inputId = $button.attr("data-qty-input-id");
    const quantity = inputId ? parseInt($(`#${inputId}`).val(), 10) : parseInt($button.attr("data-quantity"), 10);

    return Number.isFinite(quantity) && quantity > 0 ? quantity : 1;
  };

  const getDeliveryAddress = (sessionData) => ({
    address_name: sessionData.address_name || sessionData.delivery_address,
    lat: sessionData.lat,
    lng: sessionData.lng,
    postal: sessionData.postal,
    road_name: sessionData.road_name,
    blk_no: sessionData.blk_no,
    building: sessionData.building,
  });

  const hasSavedShipping = (sessionData) => {
    if (!sessionData || !sessionData.order_mode || !sessionData.outlet_id || !sessionData.date || !sessionData.time) {
      return false;
    }

    if (sessionData.order_mode !== "delivery") {
      return true;
    }

    return Boolean(sessionData.delivery_address && sessionData.lat && sessionData.lng);
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

  const addToCartWithSession = (productId, quantity, sessionData) => {
    const params = {
      product_id: String(productId),
      quantity,
      order_mode: sessionData.order_mode,
      outlet_id: String(sessionData.outlet_id),
      date: sessionData.date,
      time: sessionData.time,
      hide: false,
    };

    if (sessionData.order_mode === "delivery") {
      params.delivery_address = getDeliveryAddress(sessionData);
    }

    return $.ajax({
      url: "/wp-json/zippy-addons/v1/add-to-cart",
      type: "POST",
      data: JSON.stringify(params),
      contentType: "application/json",
      headers: getApiHeaders(),
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

  $(document).on("click", ".lightbox-zippy-btn", async function (e) {
    e.preventDefault();
 
    const prod_id = $(this).data("prod");
    const product_id = $(this).data("product_id");
    const final_id = prod_id ? prod_id : product_id;
    const $button = $(this);

    if (!final_id || $button.hasClass("is-loading")) {
      return;
    }

    $button.addClass("is-loading");

    try {
      const quantity = getQuantity($button);
      openShippingPopup(final_id, quantity);
    } catch (error) {
      console.error("Add to cart failed:", error);
    } finally {
      $button.removeClass("is-loading");
    }
  });

  $(document).on("click", ".btn-close-lightbox", function () {
    $(".mfp-close").trigger("click");
  });

});
