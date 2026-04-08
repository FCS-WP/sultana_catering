"use strict";

(function () {
  var root = document.querySelector("[data-zippy-home]");
  if (!root) {
    return;
  }

  var cartUrl = root.getAttribute("data-cart-url") || window.location.pathname;

  var tabButtons = Array.prototype.slice.call(
    root.querySelectorAll("[data-home-tab]")
  );
  var tabPanels = Array.prototype.slice.call(
    root.querySelectorAll("[data-home-products-panel]")
  );

  var setActiveTab = function (activeKey) {
    tabButtons.forEach(function (button) {
      var isActive = button.getAttribute("data-home-tab") === activeKey;
      button.classList.toggle("is-active", isActive);
    });

    tabPanels.forEach(function (panel) {
      var isActive = panel.getAttribute("data-home-products-panel") === activeKey;
      panel.classList.toggle("d-none", !isActive);
      panel.hidden = !isActive;
    });
  };

  tabButtons.forEach(function (tabButton) {
    tabButton.addEventListener("click", function () {
      var activeKey = tabButton.getAttribute("data-home-tab") || "";
      setActiveTab(activeKey);
    });
  });

  if (tabButtons.length > 0) {
    var initialActiveTabButton =
      root.querySelector("[data-home-tab].is-active") || tabButtons[0];
    var initialActiveKey =
      initialActiveTabButton.getAttribute("data-home-tab") || "";
    setActiveTab(initialActiveKey);
  }

  root.querySelectorAll("[data-qty-control]").forEach(function (control) {
    var input = control.querySelector("[data-qty-input], input.qty");
    if (!input) {
      return;
    }

    var toNumber = function (raw, fallback) {
      var parsed = parseFloat(raw);
      if (!Number.isFinite(parsed)) {
        return fallback;
      }
      return parsed;
    };

    var getMin = function () {
      var min = toNumber(input.getAttribute("min"), 1);
      return min > 0 ? min : 1;
    };

    var getMax = function () {
      var max = toNumber(input.getAttribute("max"), Number.POSITIVE_INFINITY);
      return max > 0 ? max : Number.POSITIVE_INFINITY;
    };

    var getStep = function () {
      var step = toNumber(input.getAttribute("step"), 1);
      return step > 0 ? step : 1;
    };

    var sanitizeValue = function (nextValue) {
      var min = getMin();
      var max = getMax();
      var parsed = toNumber(nextValue, min);
      if (parsed < min) {
        parsed = min;
      }
      if (parsed > max) {
        parsed = max;
      }
      return parsed;
    };

    control.addEventListener("click", function (event) {
      var actionButton = event.target.closest("[data-qty-action]");
      if (!actionButton) {
        return;
      }

      var currentValue = sanitizeValue(input.value);
      var step = getStep();
      var nextValue = currentValue;
      if (actionButton.getAttribute("data-qty-action") === "minus") {
        nextValue = currentValue - step;
      }
      if (actionButton.getAttribute("data-qty-action") === "plus") {
        nextValue = currentValue + step;
      }
      input.value = String(sanitizeValue(nextValue));
      input.dispatchEvent(new Event("change", { bubbles: true }));
    });

    input.addEventListener("change", function () {
      input.value = String(sanitizeValue(input.value));
    });
  });

  root.querySelectorAll("[data-add-cart]").forEach(function (addToCartLink) {
    addToCartLink.addEventListener("click", function (event) {
      var productId =
        addToCartLink.getAttribute("data-product-id") ||
        addToCartLink.getAttribute("data-product_id");
      if (!productId) {
        event.preventDefault();
        return;
      }

      var inputId = addToCartLink.getAttribute("data-qty-input-id");
      var qtyInput = inputId ? document.getElementById(inputId) : null;
      var quantity = qtyInput ? parseInt(qtyInput.value, 10) : 1;

      if (Number.isNaN(quantity) || quantity < 1) {
        quantity = 1;
      }

      var productUrl =
        addToCartLink.getAttribute("data-product-url") ||
        addToCartLink.getAttribute("href") ||
        cartUrl;
      var targetUrl = new URL(productUrl, window.location.origin);
      if (!targetUrl.searchParams.get("add-to-cart")) {
        targetUrl.searchParams.set("add-to-cart", productId);
      }
      targetUrl.searchParams.set("quantity", String(quantity));

      if (addToCartLink.classList.contains("add_to_cart_button")) {
        addToCartLink.setAttribute("data-quantity", String(quantity));
      }
      addToCartLink.setAttribute("href", targetUrl.toString());
    });
  });
})();
