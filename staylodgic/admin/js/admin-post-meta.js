jQuery(document).ready(function ($) {
	"use strict";

	if (typeof $.fn.wpColorPicker === "function") {
		$(".colorswatch").wpColorPicker();
	}

	function acitivity_schedule() {
		$(document).on("click", ".remove-time-input", function () {
			$(this).closest(".time-input-wrapper").remove();
		});
		$(document).on("click", ".day-schedule .add-time-input", function () {
			var daySchedule = $(this).closest(".day-schedule");
			var newTimeInput = $(
				'<input type="time" name="staylodgic_activity_schedule[' +
				daySchedule.attr("id").replace("day_schedule_", "") +
				'][]" value="">'
			);
			daySchedule.find(".time-inputs").append(newTimeInput);
		});
	}
	acitivity_schedule();

	$(document).on("click", ".create-guest-registration", function (e) {
		e.preventDefault(); // Prevent default anchor action

		var stay_booking_number = $(this).data("bookingnumber"); // Get the booking number from the button's data attribute

		// AJAX call to the back-end
		$.ajax({
			url: ajaxurl, // This variable is automatically defined by WordPress in the admin
			type: "POST",
			data: {
				action: "create_guest_registration", // The action hook name
				stay_booking_number: stay_booking_number, // Pass the booking number to the back-end
				nonce: staylodgic_admin_vars.nonce,
			},
			success: function (response) {
				// On successful AJAX response, reload the page
				window.location.reload();
			},
			error: function () {
				// Handle error
				alert("Error creating guest registration.");
			},
		});
	});

	function DeleteRegistration() {
		var guestIdToDelete = null;

		$(".delete-registration").click(function (e) {
			e.preventDefault();
			guestIdToDelete = $(this).data("guest-id");
			$("#deleteConfirmationModal").show();
		});

		$(".close-button, #cancelDelete").click(function (e) {
			e.preventDefault(); // Prevent default action
			$("#deleteConfirmationModal").hide();
		});

		$("#confirmDelete").click(function (e) {
			e.preventDefault(); // Prevent default action
			if (guestIdToDelete !== null) {
				$.ajax({
					url: ajaxurl,
					type: "POST",
					data: {
						action: "delete_registration",
						post_id: staylodgic_admin_vars.post_id,
						nonce: staylodgic_admin_vars.nonce,
						guest_id: guestIdToDelete,
					},
					success: function (response) {
						if (response.success) {
							location.reload(); // Refresh the page
						} else {
							alert("Failed to delete registration.");
						}
					},
				});
			}
			$("#deleteConfirmationModal").hide();
			guestIdToDelete = null;
		});
	}
	DeleteRegistration();

	function GenerateQrCode() {
		$("#generate-qr-code").on("click", function (e) {
			e.preventDefault();
			$.ajax({
				url: ajaxurl,
				type: "POST",
				data: {
					action: "get_guest_post_permalink",
					post_id: staylodgic_admin_vars.post_id,
					nonce: staylodgic_admin_vars.nonce,
				},
				success: function (response) {
					if (response.success) {
						var qrcodeContainer = $("#qrcode");
						qrcodeContainer.empty(); // Clear the container
						new QRCode(qrcodeContainer[0], {
							text: response.data,
							// Other QR code options...
						});
					}
				},
			});
		});
	}
	GenerateQrCode();

	function PaymentsAdjuster() {
		// Attach a click event handler to the "remove" links
		$(".remove-payment").on("click", function (e) {
			e.preventDefault();
			var $removeLink = $(this);
			var timestamp = $removeLink.data("timestamp");
			var index = $removeLink.data("index");
			var $liElement = $removeLink.closest("li");

			// Remove the <li> element
			$liElement.remove();
		});
	}
	PaymentsAdjuster();

	function currencyKeyIn() {
		var totalStayNights = $(".reservation-post-numberof-nights").data(
			"numberofnights"
		);

		$('[data-priceof="roompernight"]').on("input", function (e) {
			var perNightRate = $(this).val();
			var totalRate = totalStayNights * perNightRate;
			$('[data-priceof="roomsubtotal"]').val(totalRate.toFixed(2));
			$('[data-priceof="roomtotal"]').val("");
			$(".input-tax-summary-wrap-inner").remove();
		});
		$('[data-priceof="roomsubtotal"]').on("input", function (e) {
			var totalRate = $(this).val();
			var perNightRate = totalRate / totalStayNights;
			$('[data-priceof="roompernight"]').val(perNightRate.toFixed(2));
			$('[data-priceof="roomtotal"]').val("");
			$(".input-tax-summary-wrap-inner").remove();
		});

		$(".reservation").on("input", function () {
			totalStayNights = $(".reservation-post-numberof-nights").data(
				"numberofnights"
			);
			var perNightRate = $('[data-priceof="roompernight"]').val();
			var totalRate = totalStayNights * perNightRate;
			$('[data-priceof="roomsubtotal"]').val(totalRate.toFixed(2));
			$('[data-priceof="roomtotal"]').val("");
			$(".input-tax-summary-wrap-inner").remove();
		});
	}
	currencyKeyIn();

	function currencyFormatSetter() {
		$(".currency-input").each(function () {
			// Parse the input as a floating-point number
			let value = parseFloat($(this).val());

			// Get the currency format from the data attribute
			const decimalPlaces = parseInt($(this).data("currencyformat"));

			// Format the input with the specified number of decimal places
			$(this).val(value.toFixed(decimalPlaces));
		});
	}
	currencyFormatSetter();

	document.addEventListener("click", (event) => {
		const target = event.target;
		if (!target.closest(".staylodgic-tabs a")) {
			return;
		}
		event.preventDefault();
		document.querySelectorAll(".staylodgic-tabs a").forEach((tablink) => {
			tablink.classList.remove("nav-tab-active");
		});
		target.classList.add("nav-tab-active");
		targetTab = target.getAttribute("data-tab");
		document
			.querySelectorAll(".staylodgic-options-form .staylodgic-tab-item")
			.forEach((item) => {
				if (
					item.classList.contains(`staylodgic-tab-item--${targetTab}`)
				) {
					item.style.display = "block";
				} else {
					item.style.display = "none";
				}
			});
	});
	document.addEventListener(
		"DOMContentLoaded",
		function () {
			document.querySelector(".staylodgic-tabs .nav-tab").click();
		},
		false
	);

	$("#reservation-tax-generate").on("click", function (e) {
		// Get the selected booking number
		var subtotal_for_tax = $('[data-priceof="roomsubtotal"]').val();
		var totalStayNights = $(".reservation-post-numberof-nights").data(
			"numberofnights"
		);
		var adults = $("#staylodgic_reservation_room_adults").val();
		var children = $("#staylodgic_reservation_room_children").val();

		if ("" == children) {
			children = 0;
		}

		var stay_total_guests = parseInt(adults) + parseInt(children);
		console.log("Total guests " + stay_total_guests);
		var stay_post_id = $('input[name="post_ID"]').val();

		// Make an Ajax request to fetch the room names
		$.ajax({
			url: ajaxurl, // WordPress Ajax URL
			type: "POST",
			data: {
				action: "generate_tax", // Custom Ajax action
				post_id: stay_post_id,
				nonce: staylodgic_admin_vars.nonce,
				subtotal: subtotal_for_tax,
				staynights: totalStayNights,
				total_guests: stay_total_guests,
			},
			success: function (response) {
				console.log(response);
				// Handle the Ajax response
				// Display the room names in the desired element
				$("#input-tax-summary").html(response.html);

				var floatTotal = parseFloat(response.total);
				$("#staylodgic_reservation_total_room_cost").val(
					floatTotal.toFixed(2)
				);
			},
			error: function (xhr, status, error) {
				// Handle any errors that occur during the Ajax request
				console.log(xhr.responseText);
			},
		});
	});

	$("#reservation-tax-exclude").on("click", function (e) {
		// Get the selected booking number
		var subtotal_for_tax = $('[data-priceof="roomsubtotal"]').val();
		var stay_post_id = $('input[name="post_ID"]').val();

		// Make an Ajax request to fetch the room names
		$.ajax({
			url: ajaxurl, // WordPress Ajax URL
			type: "POST",
			data: {
				action: "exclude_tax", // Custom Ajax action
				post_id: stay_post_id,
				subtotal: subtotal_for_tax,
				nonce: staylodgic_admin_vars.nonce,
			},
			success: function (response) {
				console.log(response);
				// Handle the Ajax response
				// Display the room names in the desired element
				$("#staylodgic_reservation_total_room_cost").val(
					subtotal_for_tax
				);
				$(".input-tax-summary-wrap-inner").remove();
				$("#input-tax-summary").html(
					'<div class="input-tax-summary-wrap-inner">' +
					response +
					"</div>"
				);
			},
			error: function (xhr, status, error) {
				// Handle any errors that occur during the Ajax request
				console.log(xhr.responseText);
			},
		});
	});

	$("#staylodgic_payment_booking_id").on("select2:select", function (e) {
		// Get the selected booking number
		var stay_booking_number = $(this).val();

		// Make an Ajax request to fetch the room names
		$.ajax({
			url: ajaxurl, // WordPress Ajax URL
			type: "POST",
			data: {
				action: "get_room_names", // Custom Ajax action
				booking_number: stay_booking_number, // Pass the selected booking number as data
				nonce: staylodgic_admin_vars.nonce,
			},
			success: function (response) {
				// Handle the Ajax response
				// Display the room names in the desired element
				$("#payment-reservation-details").html(response);
			},
			error: function (xhr, status, error) {
				// Handle any errors that occur during the Ajax request
				console.log(xhr.responseText);
			},
		});
	});

	// Set initial positions of switches
	$(".switch-toggle").each(function () {
		var hiddenInput = $(this).find(".meta-switch-toggle");
		var currentValue = hiddenInput.val();

		if (!currentValue || currentValue == "0") {
			$(this).removeClass("switch-on");
			$(this).addClass("switch-off");
		} else {
			$(this).removeClass("switch-off");
			$(this).addClass("switch-on");
		}
	});

	// Listen for changes on #staylodgic_existing_customer
	$("#staylodgic_existing_customer").on("change", function () {
		// Check if the value is not 'none'
		if ($(this).val() !== "none") {
			// Trigger change on #staylodgic_customer_choice
			$("#staylodgic_customer_choice").val("existing").trigger("change");
		} else {
			// Trigger change on #staylodgic_customer_choice
			$("#staylodgic_customer_choice").val("new").trigger("change");
		}
	});

	// Listen for click on .choice-customer-existing
	$(".choice-customer-existing").on("click", function () {
		// Check if the value of #staylodgic_existing_customer is not 'none'
		if ($("#staylodgic_existing_customer").val() !== "none") {
			$("#staylodgic_customer_choice").val("existing").trigger("change");
		} else {
			$("#staylodgic_customer_choice").val("new").trigger("change");
		}
		// Show the existing customers select input
		$(".metabox_existing_customers").show();
	});

	// Attach click event listener to each switch-toggle
	$(".switch-toggle").on("click", function () {
		var hiddenInput = $(this).find(".meta-switch-toggle");
		var currentValue = hiddenInput.val();
		var newValue = !currentValue || currentValue == "0" ? "1" : "0";

		hiddenInput.val(newValue);
		if (newValue == "0") {
			$(this).removeClass("switch-on");
			$(this).addClass("switch-off");
		} else {
			$(this).removeClass("switch-off");
			$(this).addClass("switch-on");
		}
	});

	$(".metabox-image-radio-selector").on("click", function () {
		var check_radio_selector = $(this).data("holder");
		$("#" + check_radio_selector).prop("checked", true);
	});

	$(".movethis-wrap .add-box").click(function () {
		var repeat_id = $(".movethis-wrap").data("repeat");
		var n = $(".text-box").length + 1;
		if (4 < n) {
			$(".add-box-notice").fadeIn();
			setTimeout("jQuery('.add-box-notice').fadeOut();", 4000);
			return false;
		}
		var box_html = $(
			'<div id="text-box" class="text-box" id="text-box"><input placeholder="Age" type="text" name="' +
			repeat_id +
			'[age][]" value="" id="box_age' +
			n +
			'" /><a href="#" class="remove-box">Remove</a></div>'
		);
		box_html.hide();
		$(".movethis-wrap .text-box:last").after(box_html);
		box_html.fadeIn("slow");
		return false;
	});
	$(".movethis-wrap").on("click", ".remove-box", function () {
		$(this).parent().css("background-color", "#FF6C6C");
		$(this)
			.parent()
			.fadeOut("slow", function () {
				$(this).remove();
				$(".box-number").each(function (index) {
					$(this).text(index + 1);
				});
			});
		return false;
	});

	$(document).on("click", "#add-bed-setup-button", function () {
		// Generate a unique identifier based on the current date and time
		const unique_id =
			new Date().getTime() + Math.floor(Math.random() * 1000);

		// Create a container for the new bed setup using jQuery
		const container = $("<div></div>").addClass(
			"bed-setup-dynamic-container"
		);
		// Set the data attribute on the container
		container.attr("data-unique-id", unique_id);

		// Set the HTML code for the new bed setup
		container.html($("#bed_setup_container").html());

		// Get the container where new bed setups should be added using jQuery
		const bedSetupsContainer = $("#bed-inputs-container");

		// Append the new bed setup to the container
		bedSetupsContainer.append(container);
		container.find(".add-bedlayout-box").click();
		// bedSetupsContainer.find('.newbed-layout select,.newbed-layout input').prop('disabled', false);
	});

	// Add button click event
	$(document).on("click", ".bedlayout-wrap .add-bedlayout-box", function () {
		var bedlayoutWrap = $(this).closest(".bedlayout-wrap");

		// Clone the div section
		var newSection = bedlayoutWrap
			.find(".bedlayout-box")
			.first()
			.clone(true);

		// Create new IDs for the cloned select input and the input field
		var newBedlayoutBoxId =
			"bedlayout-box" + bedlayoutWrap.find(".bedlayout-box").length;
		var newInputId =
			"bed_number" + bedlayoutWrap.find(".bedlayout-box").length;

		// Update the ID of the select input and the input field in the cloned section
		newSection.attr("id", newBedlayoutBoxId);
		newSection.find("input").attr("id", newInputId);

		var unique_id = bedlayoutWrap
			.parent(".bed-setup-dynamic-container")
			.data("unique-id");
		console.log(unique_id);

		// Reset the input field value in the cloned section
		newSection.find("input").val("");
		newSection.find("select,input").prop("disabled", false);

		// Update the name attribute for select input (bedtype) with the generated unique_id
		newSection
			.find("select")
			.attr(
				"name",
				"staylodgic_alt_bedsetup[" + unique_id + "][bedtype][]"
			);

		// Update the name attribute for input (bednumber) with the generated unique_id
		newSection
			.find("input")
			.attr(
				"name",
				"staylodgic_alt_bedsetup[" + unique_id + "][bednumber][]"
			);

		// Add a remove button to the cloned section
		var removeButton = $('<div class="remove-bedlayout">Remove</div>');
		newSection.append(removeButton).addClass("newbed-layout");

		// Append the new div section below the last one
		bedlayoutWrap.find(".bedlayout-box").last().after(newSection);

	});
	// Remove button click event
	$("body").on("click", ".remove-bedlayout", function () {
		$(this).closest(".bedlayout-box").remove();
	});

	// Add button click event
	$(".taxlayout-wrap .add-taxlayout-box").click(function () {
		var taxlayoutWrap = $(this).closest(".taxlayout-wrap");

		// Destroy select2 instances if they exist
		taxlayoutWrap.find(".chosen-select-metabox").each(function () {
			if ($(this).data("select2")) {
				$(this).select2("destroy");
			}
		});

		// Clone the div section
		var newSection = taxlayoutWrap
			.find(".taxlayout-box")
			.first()
			.clone(true);

		// Reset the input field value in the cloned section
		newSection.find("input").val("");

		// Append the new div section below the last one
		taxlayoutWrap.find(".taxlayout-box").last().after(newSection);

		// Generate new IDs for the cloned select input and the input field
		var clonedSections = taxlayoutWrap.find(".taxlayout-box");
		var numClonedSections = clonedSections.length;

		clonedSections.each(function (index) {
			var section = $(this);
			var taxlabelInput = section.find(
				'input[name^="staylodgic_bedsetup_repeat[taxlabel]"]'
			);
			var taxtypeSelect = section.find(
				'select[name^="staylodgic_bedsetup_repeat[taxtype]"]'
			);
			var taxnumberInput = section.find(
				'input[name^="staylodgic_bedsetup_repeat[taxnumber]"]'
			);

			var newTaxlabelId = "tax_label" + index;
			var newTaxnumberId = "tax_number" + index;

			taxlabelInput.attr("id", newTaxlabelId);
			taxnumberInput.attr("id", newTaxnumberId);

			if (
				taxtypeSelect.length > 0 &&
				taxtypeSelect.hasClass("chosen-select-metabox")
			) {
				var newTaxtypeSelectId =
					"tax_type_staylodgic_bedsetup_repeat_" + index;
				taxtypeSelect.attr("id", newTaxtypeSelectId);
			}
		});

		// Add a remove button to the cloned section
		var removeButton = $('<div class="remove-taxlayout">Remove</div>');
		newSection.append(removeButton);

		// Re-initialize select2
		if (
			taxtypeSelect.length > 0 &&
			taxtypeSelect.hasClass("chosen-select-metabox")
		) {
			taxlayoutWrap.find(".chosen-select-metabox").select2();
		}
	});

	// Remove button click event
	$("body").on("click", ".remove-taxlayout", function () {
		$(this).closest(".taxlayout-box").remove();
	});

	/**
	 * Google Fonts
	 */
	function MetaBoxGoogleFontSelect(slctr, mainID) {
		var _selected = $(slctr).val();
		var _fontname = $(slctr).find(":selected").data("font");
		var _linkclass = "style_link_" + mainID;
		var _previewer = mainID + "_metabox_googlefont_previewer";

		if (_selected) {
			$("." + _previewer).fadeIn();

			if (_selected !== "0" && _selected !== "Default Font") {
				$("." + _linkclass).remove();

				var the_font = _selected.replace(/\s+/g, "+");

				$("head").append(
					'<link href="https://fonts.googleapis.com/css?family=' +
					the_font +
					'" rel="stylesheet" class="' +
					_linkclass +
					'">'
				);

				$("." + _previewer).css(
					"font-family",
					_fontname + ", sans-serif"
				);
			} else {
				$("." + _previewer).css("font-family", "");
				$("." + _previewer).fadeOut();
			}
		}
	}

	//init for each element
	jQuery(".metabox_google_font_select").each(function () {
		var mainID = jQuery(this).attr("id");
		MetaBoxGoogleFontSelect(this, mainID);
	});

	//init when value is changed
	jQuery(".metabox_google_font_select").change(function () {
		var mainID = jQuery(this).attr("id");
		MetaBoxGoogleFontSelect(this, mainID);
	});

	if ($.fn.select2 && $(".chosen-select-metabox").length) {
		$(".chosen-select-metabox").select2({
			width: "220px", // Sets the width to 220 pixels
		});
	}

	var sidebarlist;
	sidebarlist = $(".page_style img.of-radio-img-selected").attr("data-value");
	if (sidebarlist == "nosidebar") {
		$(".sidebar_choice").hide();
	}

	var videoembedcode;
	videoembedcode = $(".portfolio_header img.of-radio-img-selected").attr(
		"data-value"
	);

	if (videoembedcode != "Video") {
		$(".videoembed").hide();
	}

	var linkmethod;
	linkmethod = $(".thumbnail_linktype img.of-radio-img-selected").attr(
		"data-value"
	);

	if (linkmethod == "meta_thumbnail_direct") {
		$(".portfoliolinktype").hide();
	}

	$(".of-radio-img-selected").each(function () {
		var toggleClass = $(this)
			.parent()
			.find("span")
			.attr("data-toggleClass");
		var toggleAction = $(this)
			.parent()
			.find("span")
			.attr("data-toggleAction");
		var toggleTrigger = $(this)
			.parent()
			.find("span")
			.attr("data-toggleTrigger");
		var toggleID = $(this).parent().find("span").attr("data-toggleID");
		var toggleClass = $(this)
			.parent()
			.find("span")
			.attr("data-toggleClass");
		var parentclass = $(this)
			.parent()
			.find("span")
			.attr("data-parentclass");
		var SteppingStone = $(this).attr("data-value");

		if ($(this).parent().hasClass("trigger_element")) {
			$("." + parentclass + "-trigger").hide();
			$("." + parentclass + "-" + SteppingStone).show();
		}
	});

	// Image Options
	$(".of-radio-img-img").on("click", function () {
		$(this)
			.parent()
			.find(".of-radio-img-img")
			.removeClass("of-radio-img-selected");
		$(this).addClass("of-radio-img-selected");

		var toggleClass = $(this)
			.parent()
			.find("span")
			.attr("data-toggleClass");
		var toggleAction = $(this)
			.parent()
			.find("span")
			.attr("data-toggleAction");
		var toggleTrigger = $(this)
			.parent()
			.find("span")
			.attr("data-toggleTrigger");
		var toggleID = $(this).parent().find("span").attr("data-toggleID");
		var toggleClass = $(this)
			.parent()
			.find("span")
			.attr("data-toggleClass");
		var parentclass = $(this)
			.parent()
			.find("span")
			.attr("data-parentclass");
		var SteppingStone = $(this).attr("data-value");

		if ($(this).parent().hasClass("trigger_element")) {
			$("." + parentclass + "-trigger").hide();
			$("." + parentclass + "-" + SteppingStone).show();
		}
	});

	//jQuery( ".ranger-bar :text" ).slider();
	$(".ranger-bar :text").each(function (index) {
		// get input ID
		var inputField = $(this);
		var inputId = $(this).attr("id");
		// get input value
		var inputValue = parseInt($(this).val());
		// get input max
		var inputMin = parseInt($(this).attr("min"));
		var inputMax = parseInt($(this).attr("max"));

		$("#" + inputId + "_slider").slider({
			range: "min",
			value: inputValue,
			max: inputMax,
			min: inputMin,
			slide: function (event, ui) {
				$(inputField).val(ui.value);
			},
		});
	});

	$(".ranger-bar :text").change(function () {
		var inputField = $(this);
		var inputId = $(this).attr("id");
		var inputMin = parseInt($(this).attr("min"));
		var inputMax = parseInt($(this).attr("max"));
		var inputValue = parseInt($(this).val());

		if (inputValue > inputMax) {
			inputValue = inputMax;
			$(inputField).val(inputValue);
		}
		if (inputValue < inputMin) {
			inputValue = inputMin;
			$(inputField).val(inputValue);
		}
		$("#" + inputId + "_slider").slider("value", inputValue);
	});

	jQuery(".selectbox-wrap select").each(function () {
		jQuery(this).wrap('<div class="selectbox"/>');
		jQuery(this).after(
			"<span class='selecttext'></span><span class='select-arrow'></span>"
		);
		var val = jQuery(this).children("option:selected").text();
		jQuery(this).next(".selecttext").text(val);
		jQuery(this).change(function () {
			var val = jQuery(this).children("option:selected").text();
			jQuery(this).next(".selecttext").text(val);
		});
	});

	function responsiveDataFields() {
		$(document).on("focus", ".responsive-data-text", function () {
			var inResponsiveCue = $(this).prev(".responsive-data-media");
			$(this).keyup(function (e) {
				var responsiveText = $(this).val();
				var responsiveData = responsiveText.split(",");

				var responsiveDataDesktop = responsiveData[0];
				var responsiveDataTablet = responsiveData[0];
				var responsiveDataMobile = responsiveData[0];

				if (typeof responsiveData[1] !== "undefined") {
					responsiveDataTablet = responsiveData[1];
				}
				if (responsiveDataTablet) {
					responsiveDataMobile = responsiveDataTablet;
				}
				if (typeof responsiveData[2] !== "undefined") {
					responsiveDataMobile = responsiveData[2];
				}

				inResponsiveCue
					.find(".responsive-data-desktop")
					.text(responsiveDataDesktop.trim());
				inResponsiveCue
					.find(".responsive-data-tablet")
					.text(responsiveDataTablet.trim());
				inResponsiveCue
					.find(".responsive-data-mobile")
					.text(responsiveDataMobile.trim());
			});
		});
	}
	responsiveDataFields();

	function singleImageUpload() {
		// ******* Uploader Function
		var custom_uploader, curr_upload_button, input_field_id, attachment;

		$(".button-shortcodegen-uploader").on("click", function (e) {
			e.preventDefault();

			curr_upload_button = $(this);

			input_field_id = $(this).data("id");

			//If the uploader object has already been created, reopen the dialog
			if (custom_uploader) {
				custom_uploader.open();
				return;
			}

			//Extend the wp.media object
			custom_uploader = wp.media.frames.file_frame = wp.media({
				title: "Choose Image",
				button: {
					text: "Choose Image",
				},
				multiple: false,
			});

			//When a file is selected, grab the URL and set it as the text field's value
			custom_uploader.on("select", function () {
				attachment = custom_uploader
					.state()
					.get("selection")
					.first()
					.toJSON();
				$(curr_upload_button)
					.prev("input#" + input_field_id)
					.val(attachment.url);
				//$('#' + input_field_id ).val(attachment.url);
			});

			//Open the uploader dialog
			custom_uploader.open();
		});
	}
	singleImageUpload();

	// ******* Multi Upload Function
	var frame,
		images = staylodgic_admin_vars.post_gallery,
		proofingimages = staylodgic_admin_vars.proofing_gallery,
		proofingSelection = proofingLoadImages(proofingimages),
		selection = loadImages(images);

	$("body").addClass("mtheme-admin-core-on");

	// Load images
	function loadImages(images) {
		if (images && images !== "false" && images.trim() !== "") {
			console.log( 'ererere');
			var shortcode = new wp.shortcode({
				tag: "gallery",
				attrs: { ids: images },
				type: "single",
			});

			var attachments = wp.media.gallery.attachments(shortcode);

			var selection = new wp.media.model.Selection(attachments.models, {
				props: attachments.props.toJSON(),
				multiple: true,
			});

			selection.gallery = attachments.gallery;

			// Fetch the query's attachments, and then break ties from the
			// query to allow for sorting.
			selection.more().done(function () {
				// Break ties with the query.
				selection.props.set({ query: false });
				selection.unmirror();
				selection.props.unset("orderby");
			});

			return selection;
		}

		// Return an empty selection so gallery can still be created
		return new wp.media.model.Selection([], {
			props: {},
			multiple: true,
		});
	}

	$("#staylodgic_images_upload").on("click", function (e) {
		e.preventDefault();

		// Always get the latest image IDs from the hidden field
		var images = $("#staylodgic_image_ids").val();
		var selection = loadImages(images);

		// Set options for 1st frame render
		var options = {
			title: "Create Featured Gallery",
			state: "gallery-edit",
			frame: "post",
			selection: selection,
		};

		// Check if frame or gallery already exist
		if (frame || selection) {
			options["title"] = "Edit Featured Gallery";
		}

		frame = wp.media(options).open();

		// Tweak views
		frame.menu.get("view").unset("cancel");
		frame.menu.get("view").unset("separateCancel");
		frame.menu.get("view").get("gallery-edit").el.innerHTML =
			"Edit Featured Gallery";
		frame.content.get("view").sidebar.unset("gallery"); // Hide Gallery Settings in sidebar

		// When we are editing a gallery
		overrideGalleryInsert();
		frame.on("toolbar:render:gallery-edit", function () {
			overrideGalleryInsert();
		});

		frame.on("content:render:browse", function (browser) {
			if (!browser) return;
			// Hide Gallery Settings in sidebar
			browser.sidebar.on("ready", function () {
				browser.sidebar.unset("gallery");
			});
		});

		// All images removed
		frame
			.state()
			.get("library")
			.on("remove", function () {
				var models = frame.state().get("library");
				if (models.length == 0) {
					selection = false;
					$.post(ajaxurl, {
						ids: "",
						action: "staylodgic_save_images",
						post_id: staylodgic_admin_vars.post_id,
						nonce: staylodgic_admin_vars.nonce,
					});
				}
			});

		// Override insert button
		function overrideGalleryInsert() {
			frame.toolbar.get("view").set({
				insert: {
					style: "primary",
					text: "Save Featured Gallery",

					click: function () {
						var models = frame.state().get("library"),
							ids = "";

						models.each(function (attachment) {
							ids += attachment.id + ",";
						});

						this.el.innerHTML = "Saving...";

						$.ajax({
							type: "POST",
							url: ajaxurl,
							data: {
								ids: ids,
								action: "staylodgic_save_images",
								post_id: staylodgic_admin_vars.post_id,
								nonce: staylodgic_admin_vars.nonce,
							},
							success: function () {
								images = ids;
								selection = loadImages(images);
								$("#staylodgic_image_ids").val(ids);
								frame.close();
							},
							dataType: "html",
						}).done(function (data) {
							$(".mtheme-gallery-thumbs").html(data);
						});
					},
				},
			});
		}
	});

	$("#staylodgic_proofing_images_upload").on("click", function (e) {
		e.preventDefault();

		// Set options for 1st frame render
		var options = {
			title: "Create Proofing Gallery",
			state: "gallery-edit",
			frame: "post",
			selection: proofingSelection,
		};

		console.log(options.proofingSelection);

		// Check if frame or gallery already exist
		if (frame || proofingSelection) {
			options["title"] = "Edit Proofing Gallery";
		}

		frame = wp.media(options).open();

		// Tweak views
		frame.menu.get("view").unset("cancel");
		frame.menu.get("view").unset("separateCancel");
		frame.menu.get("view").get("gallery-edit").el.innerHTML =
			"Edit Proofing Gallery";
		frame.content.get("view").sidebar.unset("gallery"); // Hide Gallery Settings in sidebar

		// When we are editing a gallery
		overrideProofingGalleryInsert();
		frame.on("toolbar:render:gallery-edit", function () {
			overrideProofingGalleryInsert();
		});

		frame.on("content:render:browse", function (browser) {
			if (!browser) return;
			// Hide Gallery Settings in sidebar
			browser.sidebar.on("ready", function () {
				browser.sidebar.unset("gallery");
			});
		});

		// All images removed
		frame
			.state()
			.get("library")
			.on("remove", function () {
				var models = frame.state().get("library");
				if (models.length == 0) {
					proofingSelection = false;
					$.post(ajaxurl, {
						ids: "",
						action: "themecore_save_proofing_images",
						post_id: staylodgic_admin_vars.post_id,
						nonce: staylodgic_admin_vars.nonce,
					});
				}
			});

		// Override insert button
		function overrideProofingGalleryInsert() {
			frame.toolbar.get("view").set({
				insert: {
					style: "primary",
					text: "Save Proofing Gallery",

					click: function () {
						var models = frame.state().get("library"),
							ids = "";

						models.each(function (attachment) {
							ids += attachment.id + ",";
						});

						this.el.innerHTML = "Saving...";

						$.ajax({
							type: "POST",
							url: ajaxurl,
							data: {
								ids: ids,
								action: "themecore_save_proofing_images",
								post_id: staylodgic_admin_vars.post_id,
								nonce: staylodgic_admin_vars.nonce,
							},
							success: function () {
								proofingSelection = proofingLoadImages(ids);
								$("#_mtheme_proofing_image_ids").val(ids);
								frame.close();
							},
							dataType: "html",
						}).done(function (data) {
							$(".mtheme-proofing-gallery-thumbs").html(data);
						});
					},
				},
			});
		}
	});

	// Load images
	function proofingLoadImages(proofingimages) {
		if (proofingimages) {
			var shortcode = new wp.shortcode({
				tag: "gallery",
				attrs: { ids: proofingimages },
				type: "single",
			});

			var attachments = wp.media.gallery.attachments(shortcode);

			var proofingSelection = new wp.media.model.Selection(
				attachments.models,
				{
					props: attachments.props.toJSON(),
					multiple: true,
				}
			);

			proofingSelection.gallery = attachments.gallery;

			// Fetch the query's attachments, and then break ties from the
			// query to allow for sorting.
			proofingSelection.more().done(function () {
				// Break ties with the query.
				proofingSelection.props.set({ query: false });
				proofingSelection.unmirror();
				proofingSelection.props.unset("orderby");
			});

			return proofingSelection;
		}

		return false;
	}

	$(".meta-multi-upload").on("click", function (e) {
		e.preventDefault();

		// Load images
		function multi_loadImages(multi_images) {
			if (multi_images) {
				var shortcode = new wp.shortcode({
					tag: "gallery",
					attrs: { ids: multi_images },
					type: "single",
				});

				var attachments = wp.media.gallery.attachments(shortcode);

				var selection = new wp.media.model.Selection(
					attachments.models,
					{
						props: attachments.props.toJSON(),
						multiple: true,
					}
				);

				selection.gallery = attachments.gallery;

				// Fetch the query's attachments, and then break ties from the
				// query to allow for sorting.
				selection.more().done(function () {
					// Break ties with the query.
					selection.props.set({ query: false });
					selection.unmirror();
					selection.props.unset("orderby");
				});

				return selection;
			}

			return false;
		}

		var frame,
			thisInput = $(this),
			multi_images = $(this).data("imageset"),
			galleryid = $(this).data("galleryid"),
			selection = multi_loadImages(multi_images);

		// Set options for 1st frame render
		var options = {
			title: "Create Featured Gallery",
			state: "gallery-edit",
			frame: "post",
			selection: selection,
		};

		// Check if frame or gallery already exist
		if (frame || selection) {
			options["title"] = "Edit Featured Gallery";
		}

		frame = wp.media(options).open();

		// Tweak views
		frame.menu.get("view").unset("cancel");
		frame.menu.get("view").unset("separateCancel");
		frame.menu.get("view").get("gallery-edit").el.innerHTML =
			"Edit Featured Gallery";
		frame.content.get("view").sidebar.unset("gallery"); // Hide Gallery Settings in sidebar

		// When we are editing a gallery
		multi_overrideGalleryInsert();
		frame.on("toolbar:render:gallery-edit", function () {
			multi_overrideGalleryInsert();
		});

		frame.on("content:render:browse", function (browser) {
			if (!browser) return;
			// Hide Gallery Settings in sidebar
			browser.sidebar.on("ready", function () {
				browser.sidebar.unset("gallery");
			});
		});

		// All images removed
		frame
			.state()
			.get("library")
			.on("remove", function () {
				var models = frame.state().get("library");
				if (models.length == 0) {
					selection = false;
					$.post(ajaxurl, {
						ids: "",
						action: "multo_gallery_save_images",
						gallerysetid: galleryid,
						post_id: staylodgic_admin_vars.post_id,
						nonce: staylodgic_admin_vars.nonce,
					});
				}
			});

		// Override insert button
		function multi_overrideGalleryInsert() {
			frame.toolbar.get("view").set({
				insert: {
					style: "primary",
					text: "Save Featured Gallery",

					click: function () {
						var models = frame.state().get("library"),
							ids = "";

						models.each(function (attachment) {
							ids += attachment.id + ",";
						});

						this.el.innerHTML = "Saving...";

						$.ajax({
							type: "POST",
							url: ajaxurl,
							data: {
								ids: ids,
								gallerysetid: galleryid,
								action: "multo_gallery_save_images",
								post_id: staylodgic_admin_vars.post_id,
								nonce: staylodgic_admin_vars.nonce,
							},
							success: function () {
								selection = multi_loadImages(ids);
								$("#" + galleryid).val(ids);
								thisInput.data("imageset", ids);
								frame.close();
							},
							dataType: "html",
						}).done(function (data) {
							$(".multi-gallery-" + galleryid).html(data);
						});
					},
				},
			});
		}
	});
});

(function ($) {
	"use strict";

	wp.api.loadPromise.done(function () {
		wp.data.subscribe(function () {
			var imageWrapper = $("#image-meta-box"),
				linkWrapper = $("#link-meta-box"),
				videoWrapper = $("#video-meta-box"),
				quoteWrapper = $("#quote-meta-box"),
				audioWrapper = $("#audio-meta-box"),
				videoWrapper = $("#video-meta-box"),
				galleryWrapper = $("#gallery-meta-box"),
				imageSelector = $("#post-format-image"),
				audioSelector = $("#post-format-audio"),
				quoteSelector = $("#post-format-quote"),
				linkSelector = $("#post-format-link"),
				videoSelector = $("#post-format-video"),
				gallerySelector = $("#post-format-gallery");

			hideAll();
			showCheckedChoice();

			$(
				".block-editor-page .edit-post-sidebar .components-panel .editor-post-format select"
			)
				.change(function () {
					var postformatChoice = "";
					postformatChoice = $(this).val();
					console.log(postformatChoice);
					switch (postformatChoice) {
						case "quote":
							quoteWrapper.css("display", "block");
							DisplaySelected(quoteWrapper);
							break;

						case "gallery":
							galleryWrapper.css("display", "block");
							DisplaySelected(galleryWrapper);
							break;

						case "video":
							videoWrapper.css("display", "block");
							DisplaySelected(videoWrapper);
							break;

						case "link":
							linkWrapper.css("display", "block");
							DisplaySelected(linkWrapper);
							break;

						case "image":
							imageWrapper.css("display", "block");
							DisplaySelected(imageWrapper);
							break;

						case "audio":
							audioWrapper.css("display", "block");
							DisplaySelected(audioWrapper);
							break;

						default:
							quoteWrapper.css("display", "none");
							galleryWrapper.css("display", "none");
							videoWrapper.css("display", "none");
							linkWrapper.css("display", "none");
							audioWrapper.css("display", "none");
							imageWrapper.css("display", "none");
					}
				})
				.trigger("change");

			var postmetaClassicEditorChoice = jQuery(
				"#post-formats-select input"
			);
			postmetaClassicEditorChoice.change(function () {
				var thisElement = jQuery(this).val();

				switch (thisElement) {
					case "quote":
						quoteWrapper.css("display", "block");
						DisplaySelected(quoteWrapper);
						break;

					case "gallery":
						galleryWrapper.css("display", "block");
						DisplaySelected(galleryWrapper);
						break;

					case "video":
						videoWrapper.css("display", "block");
						DisplaySelected(videoWrapper);
						break;

					case "link":
						linkWrapper.css("display", "block");
						DisplaySelected(linkWrapper);
						break;

					case "image":
						imageWrapper.css("display", "block");
						DisplaySelected(imageWrapper);
						break;

					case "audio":
						audioWrapper.css("display", "block");
						DisplaySelected(audioWrapper);
						break;

					default:
						quoteWrapper.css("display", "none");
						galleryWrapper.css("display", "none");
						videoWrapper.css("display", "none");
						linkWrapper.css("display", "none");
						audioWrapper.css("display", "none");
						imageWrapper.css("display", "none");
				}
			});

			function DisplaySelected(elementSelected) {
				hideAll();
				elementSelected.css("display", "block");
			}

			function hideAll() {
				videoWrapper.css("display", "none");
				galleryWrapper.css("display", "none");
				quoteWrapper.css("display", "none");
				linkWrapper.css("display", "none");
				audioWrapper.css("display", "none");
				imageWrapper.css("display", "none");
			}

			function showCheckedChoice() {
				if (quoteSelector.is(":checked")) {
					quoteWrapper.css("display", "block");
				}

				if (linkSelector.is(":checked")) {
					linkWrapper.css("display", "block");
				}

				if (audioSelector.is(":checked")) {
					audioWrapper.css("display", "block");
				}

				if (gallerySelector.is(":checked")) {
					galleryWrapper.css("display", "block");
				}

				if (videoSelector.is(":checked")) {
					videoWrapper.css("display", "block");
				}

				if (imageSelector.is(":checked")) {
					imageWrapper.css("display", "block");
				}
			}
		});
	});
})(jQuery);
