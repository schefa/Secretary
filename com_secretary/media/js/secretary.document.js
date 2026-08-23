(function ($, Secretary) {
    $(document).ready(readyFn);

    function readyFn() {
        Secretary.Document = {
            counter: {
                set: function (value) {
                    $(".item-counter").attr("counter", value);
                    return;
                },
                get: function () {
                    return $(".item-counter").attr("counter");
                },
            },

            addDocument: function (id, nr, created, deadline, title, subtotal, tax, total, subjectid) {
                var counter = Secretary.Document.counter.get();
                var html = $(".table-item-document:first").html();

                html = html.replace(/##counter##/g, counter);
                html = html.replace(/##id##/g, id);
                html = html.replace(/##title##/g, title);
                html = html.replace(/##price##/g, subtotal);
                html = html.replace(/##tax##/g, tax);
                html = html.replace(/##total##/g, total);
                html = html.replace(/##deadline##/g, deadline);
                html = html.replace(/##created##/g, created);
                html = html.replace(/##nr##/g, nr);
                html = html.replace(/##subjectid##/g, subjectid);

                function insertRow(subjectName) {
                    var rowHtml = html.replace(/##subject##/g, subjectName);
                    $(
                        '<div class="table-item table-item-document clearfix dd-item" data-id="' +
                            parseInt(counter) +
                            '">' +
                            rowHtml +
                            "</div>",
                    )
                        .appendTo(".table-items-list div.dd-list")
                        .show();
                    Secretary.Document.counter.set(parseInt(counter) + 1);
                    Secretary.Document.calculate.total();
                }

                if (subjectid > 0) {
                    $.ajax({
                        url: "index.php?option=com_secretary&task=ajax.search&section=subjects&id=" + subjectid,
                        success: function (data) {
                            chosenSubject = JSON.parse(data);
                            chosenSubject.fullname = (chosenSubject.firstname + " " + chosenSubject.lastname).trim();
                            insertRow(chosenSubject.fullname);
                        },
                    }).fail(function () {
                        insertRow("?");
                    });
                } else {
                    insertRow("?");
                }

                return false;
            },

            add: function (title, desc, pno, quantity, entity, price, taxRate, total) {
                if (total == "") total = 0;
                var counter = Secretary.Document.counter.get();

                // Autocomplete Product Title
                $(document).on("focus", "input.table-item-title.pro", function () {
                    var pUsage = $("select.pUsage").val();
                    $(this)
                        .autocomplete({
                            source: "index.php?option=com_secretary&task=ajax.search&section=products&u=" + pUsage,
                            minLength: 2,
                            open: function (event, ui) {
                                $(".ui-autocomplete").css("z-index", 1000);
                            },
                            select: function (event, ui) {
                                $(this).val(ui.item.value);
                                $('textarea.table-item-title[name="jform[items][' + counter + '][description]"]').val(
                                    ui.item.description,
                                );

                                var pprice = parseFloat(ui.item.price).toFixed(2);
                                $('input.table-item-price[name="jform[items][' + counter + '][price]"]').val(pprice);
                                var productTaxRate = parseFloat(ui.item.taxRate).toFixed(2);
                                $('input.table-item-taxrate[name="jform[items][' + counter + '][taxRate]"]').val(
                                    productTaxRate,
                                );

                                var parent = $(this).closest("div.table-item");
                                var qnty = parent.find(".table-item-quantity").val();
                                var item_total = Number(qnty) * Number(pprice);
                                parent.find(".table-item-total").val(item_total);
                                Secretary.Document.calculate.total();
                                return;
                            },
                        })
                        .autocomplete("instance")._renderItem = function (ul, item) {
                        return $("<li>")
                            .append(
                                '<a><span class="ui-menuitem-value">' +
                                    item.value +
                                    '</span><br><span class="ui-menuitem-sub">' +
                                    item.price +
                                    "</span></a>",
                            )
                            .appendTo(ul);
                    };
                });

                var html = $(".table-item:first").html();
                html = html.replace(/##counter##/g, counter);
                html = html.replace(/##title##/g, title);
                if (desc && desc !== "undefined") {
                    html = html.replace(/##desc##/g, desc);
                } else {
                    html = html.replace(/##desc##/g, "");
                }
                html = html.replace(/##pno##/g, pno);
                html = html.replace(/##quantity##/g, quantity);

                if (isNaN(entity)) {
                    html = html.replace(/##entity##/g, entity);
                } else {
                    var searchEntity = '<option value="' + entity + '">';
                    var replaceEntity = '<option value="' + entity + '" selected="selected">';
                    html = html.replace(searchEntity, replaceEntity);
                    html = html.replace(/##entity##/g, entity);
                }

                // Steuern abhängig vom Steuersatz und Typ
                if (typeof taxRate === "undefined" && isNaN(taxRate)) {
                    html = html.replace(/##taxRate##/g, Number(taxRatePerc));
                } else {
                    html = html.replace(/##taxRate##/g, Number(taxRate));
                }

                if (!isNaN(parseFloat(price))) {
                    price = parseFloat(price).toFixed(2);
                }
                html = html.replace(/##price##/g, price);

                if (!isNaN(parseFloat(total)) && title.length > 0) {
                    total = parseFloat(total).toFixed(2);
                } else {
                    total = "";
                }

                // if( desc != '') html.replace(/style=\"display:none;\"/g, /style=\"display:block;\"/);

                html = html.replace(/##total##/g, total);
                $('<div class="table-item clearfix dd-item" data-id="' + parseInt(counter) + '">' + html + "</div>")
                    .appendTo(".table-items-list div.dd-list")
                    .show();
                Secretary.Document.counter.set(parseInt(counter) + 1);

                Secretary.Document.calculate.total();
                return;
            },

            calculate: {
                // Reads {total, taxRate} for every item row (including linked
                // sub-documents, which have no taxrate input and therefore never
                // contribute tax, only their total to the overall sum).
                collectItems: function () {
                    var items = [];
                    $("div.table-item").each(function () {
                        items.push({
                            total: $(this).find("input.table-item-total").val(),
                            taxRate: $(this).find("input.table-item-taxrate").val(),
                        });
                    });
                    return items;
                },

                renderTaxBreakdown: function (byRate) {
                    $(".document-taxrate").empty();
                    for (var rate in byRate) {
                        if (byRate.hasOwnProperty(rate)) {
                            var amount = byRate[rate];
                            $(
                                '<div class="fullwidth"><div class="pull-right secretary-input-group document-clean-input clearfix"><div class="secretary-input-group-left"><span class="tax-proz">' +
                                    rate +
                                    '%</span><span class="tax-val">' +
                                    parseFloat(amount).toFixed(2) +
                                    '</span><input type="hidden" name="jform[taxtotal][' +
                                    rate +
                                    ']" value="' +
                                    amount.toFixed(4) +
                                    '" /></div><div class="secretary-input-group-right currency-control">' +
                                    currency +
                                    "</div></div></div>",
                            ).appendTo(".document-taxrate");
                        }
                    }
                },

                taxes: function (taxtype, rabattProz) {
                    if (Number(taxtype) === Secretary.TaxCalc.TAX_NONE) {
                        $(".document-taxrate").empty();
                        $(".table-item-taxrate").val("");
                        return 0;
                    }

                    var items = Secretary.Document.calculate.collectItems();
                    var result = Secretary.TaxCalc.aggregateTaxes(Number(taxtype), items, rabattProz);

                    Secretary.Document.calculate.renderTaxBreakdown(result.byRate);

                    return result.taxTotal;
                },

                total: function () {
                    var rabatt = $("#jform_rabatt").val();
                    var rabattProz = $("#jform_rabatt_proz").val();
                    var taxtype = Number($("#jform_taxtype").val());

                    var items = Secretary.Document.calculate.collectItems();
                    var totals = Secretary.TaxCalc.computeDocumentTotals(taxtype, items, {
                        amount: rabatt,
                        percent: rabattProz,
                    });

                    if (Number(rabatt) > 0) {
                        $("input#jform_rabatt_proz").val(totals.discountPercent.toFixed(2));
                    } else if (!(Number(rabattProz) > 0)) {
                        $("input#jform_rabatt_proz").val("");
                    }

                    Secretary.Document.calculate.renderTaxBreakdown(totals.byRateRaw);

                    $(".document-subtotal").val(totals.subtotal.toFixed(2));
                    $(".document-total").val(totals.total.toFixed(2));
                    $(".document-taxtotal").val(totals.taxTotal.toFixed(2));
                    $(".secretary-acc-sum").text(totals.total.toFixed(2));

                    if (totals.discountAmount > 0 && totals.discountPercent > 0) {
                        $("input#jform_rabatt").val(totals.discountAmount.toFixed(2));
                    }

                    if (taxtype === Secretary.TaxCalc.TAX_NONE) {
                        $(".table-item-taxrate").val("");
                    }

                    return;
                },
            },
        };

        Secretary.selectContactData = function (name, street, zip, location, phone, email, gender, id, connections) {
            $("input#jform_subject_name").val(name);
            $("input#jform_subject_street").val(street);
            $("input#jform_subject_zip").val(zip);
            $("input#jform_subject_location").val(location);
            $("input#jform_subject_phone").val(phone);
            $("input#jform_subject_email").val(email);
            $("select#jform_subject_gender").val(gender);
            $("input#jform_subjectid").val(id);

            // Display block
            $("#display_contact_name").show();
            $("#contact_name").text(name);
            $("#jform_subject_name").hide();

            var options = $("#jform_subject_connection");
            options.empty();

            if (connections.length < 1) {
                options.parent().hide();
            } else {
                options.parent().show();
                $.each(connections, function () {
                    var texti = this.note.length > 0 ? this.fullname + " (" + this.note + ")" : this.fullname;
                    options.append($("<option />").val(this.id).text(texti));
                });
            }
        };

        var productUsage = {
            value: 1,
            get: function () {
                $("#productUsage select").val();
            },
            set: function (val) {
                this.value = val;
            },
        };

        $(document).on("change", "#productUsage select", function () {
            productUsage.set($(this).val());
        });

        $(document).on("click", ".add-subject-as-contact", function () {
            Secretary.selectContactData(
                chosenSubject.fullname,
                chosenSubject.street,
                chosenSubject.zip,
                chosenSubject.location,
                chosenSubject.phone,
                chosenSubject.email,
                chosenSubject.gender,
                chosenSubject.id,
                chosenSubject.connections,
            );
        });

        ////////////////////////////////////////////
        // Autocomplete

        $("input#jform_subject_name")
            .autocomplete({
                source: "index.php?option=com_secretary&task=ajax.search&section=subjects",
                minLength: 2,
                open: function (event, ui) {
                    $(".ui-autocomplete").css("z-index", 1000);
                },
                select: function (event, ui) {
                    Secretary.selectContactData(
                        ui.item.value,
                        ui.item.street,
                        ui.item.zip,
                        ui.item.location,
                        ui.item.phone,
                        ui.item.email,
                        ui.item.gender,
                        ui.item.id,
                        ui.item.connections,
                    );
                },
            })
            .autocomplete("instance")._renderItem = function (ul, item) {
            var appendix = '<a><span class="ui-menuitem-value">' + item.value + "</span><br>";
            appendix += '<span class="ui-menuitem-sub">';
            if (item.street.length > 0) appendix += item.street + ", ";
            appendix += item.zip + " " + item.location + "</span></a>";

            return $("<li>").append(appendix).appendTo(ul);
        };

        // Check Number
        $("input.inputAjaxNumber").change(function (event) {
            event.preventDefault();

            var cont = $("#ajaxNumber");
            var res = $("#ajaxNumberResult").hide();

            var nr = $(this).val();
            var id = cont.data("id");
            var catid = cont.data("catid");

            $.ajax({
                url:
                    "index.php?option=com_secretary&task=ajax.checkDocumentNumber&nr=" +
                    nr +
                    "&catid=" +
                    catid +
                    "&id=" +
                    id,
                type: "get",
                success: function (response) {
                    res.html(response).fadeIn();
                },
            });
        });

        // Autocomplete Document Title
        $("#jform_title")
            .autocomplete({
                source: "index.php?option=com_secretary&task=ajax.search&section=document_title",
                minLength: 3,
                open: function (event, ui) {
                    $(".ui-autocomplete").css("z-index", 1000);
                },
                select: function (event, ui) {
                    $(this).val(ui.item.value);
                },
            })
            .autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                .append('<a><span class="ui-menuitem-value">' + item.value + "</span>")
                .appendTo(ul);
        };

        // Calculate die Gesamtsumme je Zeile

        $(document).on("keyup change", "div.table-item", function () {
            var quantity = $(this).find("input.table-item-quantity").val();
            var price = $(this).find("input.table-item-price").val();
            var total = quantity * price;

            if (!$(this).hasClass("table-item-document") && (total != "" || total !== 0)) {
                $(this).find("input.table-item-total").val(parseFloat(total).toFixed(2));
            }

            Secretary.Document.calculate.total();
        });

        $(document).on("keyup change", "#jform_rabatt", function () {
            $("input#jform_rabatt_proz").val("");
        });
        $(document).on("keyup change", "#jform_rabatt_proz", function () {
            $("input#jform_rabatt").val("");
        });

        $(document).on("keyup change", ".document-summary input", function () {
            Secretary.Document.calculate.total();
        });

        if (typeof e_items !== "undefined") {
            for (var e in e_items) {
                if (e_items.hasOwnProperty(e)) {
                    // Produktnummer
                    var pno = "";
                    if (typeof e_items[e].pno !== "undefined" && e_items[e].pno.length > 0) {
                        pno = htmlEntities(e_items[e].pno);
                    }
                    if (typeof e_items[e].id !== "undefined") {
                        Secretary.Document.addDocument(
                            e_items[e].id,
                            e_items[e].nr,
                            e_items[e].created,
                            e_items[e].deadline,
                            htmlEntities(e_items[e].title),
                            e_items[e].subtotal,
                            e_items[e].tax,
                            e_items[e].total,
                            e_items[e].subjectid,
                        );
                    } else {
                        Secretary.Document.add(
                            htmlEntities(e_items[e].title),
                            htmlEntities(e_items[e].description),
                            pno,
                            e_items[e].quantity,
                            htmlEntities(e_items[e].entity),
                            e_items[e].price,
                            e_items[e].taxRate,
                            e_items[e].total,
                        );
                    }
                }
            }
        }

        function htmlEntities(str) {
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;");
        }

        if ($("div.table-items-list div.dd-list").is(":empty")) {
            Secretary.Document.add("", "", "", "", "", "", taxRatePerc, "");
        }

        $("#item-add").click(function () {
            Secretary.Document.add("", "", "", "", "", "", taxRatePerc, "");
            return false;
        });

        $("#item-add-document").click(function () {
            Secretary.Document.addDocument("", "", "", "", "", "", "", "", 0);
            return false;
        });

        $("#item-toggle-tax").click(function () {
            var down = !$(this).hasClass("active");
            $.ajax({
                url: "index.php?option=com_secretary&task=ajax.toggleTaxRateColumn&v=" + +down,
            });

            $(this).toggleClass("active");
            $(".secretary-documents-table-items").toggleClass("taxSelection");
            $(".table-item-col-4").toggle();

            return false;
        });

        $("#item-toggle-pno").click(function () {
            $(this).toggleClass("active");
            $(".secretary-documents-table-items").toggleClass("pnoSelection");
            return false;
        });

        $(document).on("change", "#jform_taxtype", function (event) {
            Secretary.Document.calculate.total();
        });

        // Clears all contact information
        $(".clean-contact").click(function () {
            $("#display_contact_name").hide();
            $("#contact_name").text("");
            $("#jform_subjectid").val("");
            $("#jform_subject_name").val("");
            $("#jform_subject_street").val("");
            $("#jform_subject_zip").val("");
            $("#jform_subject_location").val("");
            $("#jform_subject_phone").val("");
            $("#jform_subject_email").val("");
            $("#jform_subject_name").show();
        });

        // Enables jQuery Plugin to reorder products list by drag & drop
        $(".table-items-list").nestable({
            dragClass: "table-item dd-dragel",
            maxDepth: 1,
            listNodeName: "div",
            itemNodeName: "div",
        });

        $(document).on("keyup change", ".table-item-quantity", function (event) {
            value = $(this).val().replace(",", ".");
            $(this).val(value);
        });

        $(document).on("focusout", ".table-item-taxrate", function () {
            Secretary.Document.calculate.total();
        });

        $(document).on("click", ".table-item-remove", function () {
            $(this).parents(".table-item").remove();
            Secretary.Document.calculate.total();
            return false;
        });

        $(document).on("click", ".open-desc", function () {
            $(this).children().toggleClass("fa-plus fa-minus");
            $(this).next("textarea").toggle();
        });

        $("#openRabattDialog").click(function () {
            $(this).toggleClass("active");
            $(".table-rabatt-row").toggle();
        });

        $(document).on("change", "#jform_currency", function (event) {
            var currency = $(this).val();
            $.ajax({
                url: "index.php?option=com_secretary&task=ajax.getCurrencySymbol&term=" + currency,
                type: "get",
                success: function (response) {
                    $(".currency-control").text(response).fadeIn();
                },
            });
        });
    }
})(jQuery, Secretary);
