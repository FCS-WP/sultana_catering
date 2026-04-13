"use strict";

import Swal from 'sweetalert2';

jQuery(document).ready(function ($) {
  var resetHomeAddToCartButton = function ($actions, $button) {
    $actions.find(".added_to_cart").remove();
    $actions.find(".zippy-home-add-cart").removeClass("added loading");

    if ($button && $button.length) {
      $button.removeClass("added loading");
    }
  };

  var hideHomeViewCartAfterDelay = function ($button) {
    $button = $($button);
    if (!$button.length) {
      return;
    }

    var $actions = $button.closest(".zippy-home-product-actions");
    if (!$actions.length) {
      return;
    }

    var existingTimer = $actions.data("zippyHomeViewCartTimer");
    if (existingTimer) {
      window.clearTimeout(existingTimer);
    }

    var cleanup = function () {
      resetHomeAddToCartButton($actions, $button);
    };

    var timer = window.setTimeout(function () {
      cleanup();
      window.setTimeout(cleanup, 150);
      window.setTimeout(cleanup, 500);
      $actions.removeData("zippyHomeViewCartTimer");
    }, 3000);

    $actions.data("zippyHomeViewCartTimer", timer);
  };

  $(document).on("click", ".zippy-home-product-actions .add_to_cart_button", function () {
    hideHomeViewCartAfterDelay($(this));
  });

  $(document).on("click", "#removeMethodShipping", function () {
    Swal.fire({
      title: "Are you sure to change order mode?",
      text: "Your current cart will be cleared",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes!",
      cancelButtonText: "Cancel",
      customClass: {
        popup: "confirmRemovePopup",
      },
      backdrop: false,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "/wp-admin/admin-ajax.php",
          type: "POST",
          data: {
            action: "remove_cart_session",
          },
          success: function (response) {
            Swal.fire({
              title: "Deleted!",
              text: "Your cart has been cleared.",
              icon: "success",
              customClass: {
                popup: "popupAlertDeleteSuccess",
              },
            }).then(() => {
              // Refresh fragments
              if (typeof wc_cart_fragments_params !== 'undefined') {
                 $(document.body).trigger('wc_fragment_refresh');
              }

              setTimeout(() => {
                window.location.href = "/";
              }, 1000);
            });
          },
          error: function () {
            Swal.fire("Error!", "Something went wrong.", "error");
          },
        });
      }
    });
  });

  $("body").on("added_to_cart", function(event, fragments, cartHash, $button) {
    if (!$button || !$button.length || !$button.hasClass("add_to_cart_button")) {
      return;
    }

    Swal.fire({
      icon: "success",
      title: "Success",
      text: "Product added to cart!",
      timer: 1500,
      showConfirmButton: false,
    });

    hideHomeViewCartAfterDelay($button);
  });
});
