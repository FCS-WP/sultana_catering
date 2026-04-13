"use strict";

import Swal from 'sweetalert2';

jQuery(document).ready(function ($) {
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
    if (!$button || !$button.hasClass("zippy-home-add-cart")) {
      return;
    }

    Swal.fire({
      icon: "success",
      title: "Success",
      text: "Product added to cart!",
      timer: 1500,
      showConfirmButton: false,
    });
  });
});
