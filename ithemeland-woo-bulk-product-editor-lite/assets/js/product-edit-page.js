jQuery(document).ready(function ($) {
  "use strict";

  $(document).on("woocommerce_variations_loaded", function () {
    if ($("#variable_product_options").length) {
      $("#variable_product_options #variable_product_options_inner").prepend(wcbeProductEditHtml.variationsTabHeader);
    }
  });
});
