/*
 * International Telephone Input v9.2.4
 * https://github.com/jackocnr/intl-tel-input.git
 * Licensed under the MIT license
 */
// wrap in UMD - see https://github.com/umdjs/umd/blob/master/jqueryPluginCommonjs.js
(function(factory) {
    if (typeof define === "function" && define.amd) {
        define([ "jquery" ], function($) {
            factory($, window, document);
        });
    } else if (typeof module === "object" && module.exports) {
        module.exports = factory(require("jquery"), window, document);
    } else {
        factory(jQuery, window, document);
    }
})(function($, window, document, undefined) {
    "use strict";
    // these vars persist through all instances of the plugin
    var pluginName = "intlTelInput", id = 1, // give each instance it's own id for namespaced event handling
    defaults = {
        // whether or not to allow the dropdown
        allowDropdown: true,
        // if there is just a dial code in the input: remove it on blur, and re-add it on focus
        autoHideDialCode: true,
        // add a placeholder in the input with an example number for the selected country
        autoPlaceholder: "polite",
        // modify the auto placeholder
        customPlaceholder: null,
        // append menu to a specific element
        dropdownContainer: "",
        // don't display these countries
        excludeCountries: [],
        // format the input value during initialisation
        formatOnInit: true,
        // geoIp lookup function
        geoIpLookup: null,
        // initial country
        initialCountry: "",
        // don't insert international dial codes
        nationalMode: true,
        // number type to use for placeholders
        numberType: "MOBILE",
        // display only these countries
        onlyCountries: [],
        // the countries at the top of the list. defaults to united states and united kingdom
        preferredCountries: [ "us", "gb" ],
        // display the country dial code next to the selected flag so it's not part of the typed number
        separateDialCode: false,
        // specify the path to the libphonenumber script to enable validation/formatting
        utilsScript: ""
    }, keys = {
        UP: 38,
        DOWN: 40,
        ENTER: 13,
        ESC: 27,
        PLUS: 43,
        A: 65,
        Z: 90,
        SPACE: 32,
        TAB: 9
    };
    // keep track of if the window.load event has fired as impossible to check after the fact
    $(window).on("load", function() {
        // UPDATE: use a public static field so we can fudge it in the tests
        $.fn[pluginName].windowLoaded = true;
    });
    function Plugin(element, options) {
        this.telInput = $(element);
        this.options = $.extend({}, defaults, options);
        // event namespace
        this.ns = "." + pluginName + id++;
        // Chrome, FF, Safari, IE9+
        this.isGoodBrowser = Boolean(element.setSelectionRange);
        this.hadInitialPlaceholder = Boolean($(element).attr("placeholder"));
    }
    Plugin.prototype = {
        _init: function() {
            // if in nationalMode, disable options relating to dial codes
            if (this.options.nationalMode) {
                this.options.autoHideDialCode = false;
            }
            // if separateDialCode then doesn't make sense to A) insert dial code into input (autoHideDialCode), and B) display national numbers (because we're displaying the country dial code next to them)
            if (this.options.separateDialCode) {
                this.options.autoHideDialCode = this.options.nationalMode = false;
            }
            // we cannot just test screen size as some smartphones/website meta tags will report desktop resolutions
            // Note: for some reason jasmine breaks if you put this in the main Plugin function with the rest of these declarations
            // Note: to target Android Mobiles (and not Tablets), we must find "Android" and "Mobile"
            this.isMobile = /Android.+Mobile|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            if (this.isMobile) {
                // trigger the mobile dropdown css
                $("body").addClass("iti-mobile");
                // on mobile, we want a full screen dropdown, so we must append it to the body
                if (!this.options.dropdownContainer) {
                    this.options.dropdownContainer = "body";
                }
            }
            // we return these deferred objects from the _init() call so they can be watched, and then we resolve them when each specific request returns
            // Note: again, jasmine breaks when I put these in the Plugin function
            this.autoCountryDeferred = new $.Deferred();
            this.utilsScriptDeferred = new $.Deferred();
            // process all the data: onlyCountries, excludeCountries, preferredCountries etc
            this._processCountryData();
            // generate the markup
            this._generateMarkup();
            // set the initial state of the input value and the selected flag
            this._setInitialState();
            // start all of the event listeners: autoHideDialCode, input keydown, selectedFlag click
            this._initListeners();
            // utils script, and auto country
            this._initRequests();
            // return the deferreds
            return [ this.autoCountryDeferred, this.utilsScriptDeferred ];
        },
        /********************
   *  PRIVATE METHODS
   ********************/
        // prepare all of the country data, including onlyCountries, excludeCountries and preferredCountries options
        _processCountryData: function() {
            // process onlyCountries or excludeCountries array if present
            this._processAllCountries();
            // process the countryCodes map
            this._processCountryCodes();
            // process the preferredCountries
            this._processPreferredCountries();
        },
        // add a country code to this.countryCodes
        _addCountryCode: function(iso2, dialCode, priority) {
            if (!(dialCode in this.countryCodes)) {
                this.countryCodes[dialCode] = [];
            }
            var index = priority || 0;
            this.countryCodes[dialCode][index] = iso2;
        },
        // filter the given countries using the process function
        _filterCountries: function(countryArray, processFunc) {
            var i;
            // standardise case
            for (i = 0; i < countryArray.length; i++) {
                countryArray[i] = countryArray[i].toLowerCase();
            }
            // build instance country array
            this.countries = [];
            for (i = 0; i < allCountries.length; i++) {
                if (processFunc($.inArray(allCountries[i].iso2, countryArray))) {
                    this.countries.push(allCountries[i]);
                }
            }
        },
        // process onlyCountries or excludeCountries array if present
        _processAllCountries: function() {
            if (this.options.onlyCountries.length) {
                // process onlyCountries option
                this._filterCountries(this.options.onlyCountries, function(inArray) {
                    // if country is in array
                    return inArray != -1;
                });
            } else if (this.options.excludeCountries.length) {
                // process excludeCountries option
                this._filterCountries(this.options.excludeCountries, function(inArray) {
                    // if country is not in array
                    return inArray == -1;
                });
            } else {
                this.countries = allCountries;
            }
        },
        // process the countryCodes map
        _processCountryCodes: function() {
            this.countryCodes = {};
            for (var i = 0; i < this.countries.length; i++) {
                var c = this.countries[i];
                this._addCountryCode(c.iso2, c.dialCode, c.priority);
                // area codes
                if (c.areaCodes) {
                    for (var j = 0; j < c.areaCodes.length; j++) {
                        // full dial code is country code + dial code
                        this._addCountryCode(c.iso2, c.dialCode + c.areaCodes[j]);
                    }
                }
            }
        },
        // process preferred countries - iterate through the preferences, fetching the country data for each one
        _processPreferredCountries: function() {
            this.preferredCountries = [];
            for (var i = 0; i < this.options.preferredCountries.length; i++) {
                var countryCode = this.options.preferredCountries[i].toLowerCase(), countryData = this._getCountryData(countryCode, false, true);
                if (countryData) {
                    this.preferredCountries.push(countryData);
                }
            }
        },
        // generate all of the markup for the plugin: the selected flag overlay, and the dropdown
        _generateMarkup: function() {
            // prevent autocomplete as there's no safe, cross-browser event we can react to, so it can easily put the plugin in an inconsistent state e.g. the wrong flag selected for the autocompleted number, which on submit could mean the wrong number is saved (esp in nationalMode)
            this.telInput.attr("autocomplete", "off");
            // containers (mostly for positioning)
            var parentClass = "intl-tel-input";
            if (this.options.allowDropdown) {
                parentClass += " allow-dropdown";
            }
            if (this.options.separateDialCode) {
                parentClass += " separate-dial-code";
            }
            this.telInput.wrap($("<div>", {
                "class": parentClass
            }));
            this.flagsContainer = $("<div>", {
                "class": "flag-container"
            }).insertBefore(this.telInput);
            // currently selected flag (displayed to left of input)
            var selectedFlag = $("<div>", {
                "class": "selected-flag"
            });
            selectedFlag.appendTo(this.flagsContainer);
            this.selectedFlagInner = $("<div>", {
                "class": "iti-flag"
            }).appendTo(selectedFlag);
            if (this.options.separateDialCode) {
                this.selectedDialCode = $("<div>", {
                    "class": "selected-dial-code"
                }).appendTo(selectedFlag);
            }
            if (this.options.allowDropdown) {
                // make element focusable and tab naviagable
                selectedFlag.attr("tabindex", "0");
                // CSS triangle
                $("<div>", {
                    "class": "iti-arrow"
                }).appendTo(selectedFlag);
                // country dropdown: preferred countries, then divider, then all countries
                this.countryList = $("<ul>", {
                    "class": "country-list hide"
                });
                if (this.preferredCountries.length) {
                    this._appendListItems(this.preferredCountries, "preferred");
                    $("<li>", {
                        "class": "divider"
                    }).appendTo(this.countryList);
                }
                this._appendListItems(this.countries, "");
                // this is useful in lots of places
                this.countryListItems = this.countryList.children(".country");
                // create dropdownContainer markup
                if (this.options.dropdownContainer) {
                    this.dropdown = $("<div>", {
                        "class": "intl-tel-input iti-container"
                    }).append(this.countryList);
                } else {
                    this.countryList.appendTo(this.flagsContainer);
                }
            } else {
                // a little hack so we don't break anything
                this.countryListItems = $();
            }
        },
        // add a country <li> to the countryList <ul> container
        _appendListItems: function(countries, className) {
            // we create so many DOM elements, it is faster to build a temp string
            // and then add everything to the DOM in one go at the end
            var tmp = "";
            // for each country
            for (var i = 0; i < countries.length; i++) {
                var c = countries[i];
                // open the list item
                tmp += "<li class='country " + className + "' data-dial-code='" + c.dialCode + "' data-country-code='" + c.iso2 + "'>";
                // add the flag
                tmp += "<div class='flag-box'><div class='iti-flag " + c.iso2 + "'></div></div>";
                // and the country name and dial code
                tmp += "<span class='country-name'>" + c.name + "</span>";
                tmp += "<span class='dial-code'>+" + c.dialCode + "</span>";
                // close the list item
                tmp += "</li>";
            }
            this.countryList.append(tmp);
        },
        // set the initial state of the input value and the selected flag by:
        // 1. extracting a dial code from the given number
        // 2. using explicit initialCountry
        // 3. picking the first preferred country
        // 4. picking the first country
        _setInitialState: function() {
            var val = this.telInput.val();
            // if we already have a dial code we can go ahead and set the flag, else fall back to default
            if (this._getDialCode(val)) {
                this._updateFlagFromNumber(val, true);
            } else if (this.options.initialCountry !== "auto") {
                // see if we should select a flag
                if (this.options.initialCountry) {
                    this._setFlag(this.options.initialCountry.toLowerCase(), true);
                } else {
                    // no dial code and no initialCountry, so default to first in list
                    this.defaultCountry = this.preferredCountries.length ? this.preferredCountries[0].iso2 : this.countries[0].iso2;
                    if (!val) {
                        this._setFlag(this.defaultCountry, true);
                    }
                }
                // if empty and no nationalMode and no autoHideDialCode then insert the default dial code
                if (!val && !this.options.nationalMode && !this.options.autoHideDialCode && !this.options.separateDialCode) {
                    this.telInput.val("+" + this.selectedCountryData.dialCode);
                }
            }
            // NOTE: if initialCountry is set to auto, that will be handled separately
            // format
            if (val) {
                // this wont be run after _updateDialCode as that's only called if no val
                this._updateValFromNumber(val, this.options.formatOnInit);
            }
        },
        // initialise the main event listeners: input keyup, and click selected flag
        _initListeners: function() {
            this._initKeyListeners();
            if (this.options.autoHideDialCode) {
                this._initFocusListeners();
            }
            if (this.options.allowDropdown) {
                this._initDropdownListeners();
            }
        },
        // initialise the dropdown listeners
        _initDropdownListeners: function() {
            var that = this;
            // hack for input nested inside label: clicking the selected-flag to open the dropdown would then automatically trigger a 2nd click on the input which would close it again
            var label = this.telInput.closest("label");
            if (label.length) {
                label.on("click" + this.ns, function(e) {
                    // if the dropdown is closed, then focus the input, else ignore the click
                    if (that.countryList.hasClass("hide")) {
                        that.telInput.focus();
                    } else {
                        e.preventDefault();
                    }
                });
            }
            // toggle country dropdown on click
            var selectedFlag = this.selectedFlagInner.parent();
            selectedFlag.on("click" + this.ns, function(e) {
                // only intercept this event if we're opening the dropdown
                // else let it bubble up to the top ("click-off-to-close" listener)
                // we cannot just stopPropagation as it may be needed to close another instance
                if (that.countryList.hasClass("hide") && !that.telInput.prop("disabled") && !that.telInput.prop("readonly")) {
                    that._showDropdown();
                }
            });
            // open dropdown list if currently focused
            this.flagsContainer.on("keydown" + that.ns, function(e) {
                var isDropdownHidden = that.countryList.hasClass("hide");
                if (isDropdownHidden && (e.which == keys.UP || e.which == keys.DOWN || e.which == keys.SPACE || e.which == keys.ENTER)) {
                    // prevent form from being submitted if "ENTER" was pressed
                    e.preventDefault();
                    // prevent event from being handled again by document
                    e.stopPropagation();
                    that._showDropdown();
                }
                // allow navigation from dropdown to input on TAB
                if (e.which == keys.TAB) {
                    that._closeDropdown();
                }
            });
        },
        // init many requests: utils script / geo ip lookup
        _initRequests: function() {
            var that = this;
            // if the user has specified the path to the utils script, fetch it on window.load, else resolve
            if (this.options.utilsScript) {
                // if the plugin is being initialised after the window.load event has already been fired
                if ($.fn[pluginName].windowLoaded) {
                    $.fn[pluginName].loadUtils(this.options.utilsScript, this.utilsScriptDeferred);
                } else {
                    // wait until the load event so we don't block any other requests e.g. the flags image
                    $(window).on("load", function() {
                        $.fn[pluginName].loadUtils(that.options.utilsScript, that.utilsScriptDeferred);
                    });
                }
            } else {
                this.utilsScriptDeferred.resolve();
            }
            if (this.options.initialCountry === "auto") {
                this._loadAutoCountry();
            } else {
                this.autoCountryDeferred.resolve();
            }
        },
        // perform the geo ip lookup
        _loadAutoCountry: function() {
            var that = this;
            // 3 options:
            // 1) already loaded (we're done)
            // 2) not already started loading (start)
            // 3) already started loading (do nothing - just wait for loading callback to fire)
            if ($.fn[pluginName].autoCountry) {
                this.handleAutoCountry();
            } else if (!$.fn[pluginName].startedLoadingAutoCountry) {
                // don't do this twice!
                $.fn[pluginName].startedLoadingAutoCountry = true;
                if (typeof this.options.geoIpLookup === "function") {
                    this.options.geoIpLookup(function(countryCode) {
                        $.fn[pluginName].autoCountry = countryCode.toLowerCase();
                        // tell all instances the auto country is ready
                        // TODO: this should just be the current instances
                        // UPDATE: use setTimeout in case their geoIpLookup function calls this callback straight away (e.g. if they have already done the geo ip lookup somewhere else). Using setTimeout means that the current thread of execution will finish before executing this, which allows the plugin to finish initialising.
                        setTimeout(function() {
                            $(".intl-tel-input input").intlTelInput("handleAutoCountry");
                        });
                    });
                }
            }
        },
        // initialize any key listeners
        _initKeyListeners: function() {
            var that = this;
            // update flag on keyup
            // (keep this listener separate otherwise the setTimeout breaks all the tests)
            this.telInput.on("keyup" + this.ns, function() {
                that._updateFlagFromNumber(that.telInput.val());
            });
            // update flag on cut/paste events (now supported in all major browsers)
            this.telInput.on("cut" + this.ns + " paste" + this.ns, function() {
                // hack because "paste" event is fired before input is updated
                setTimeout(function() {
                    that._updateFlagFromNumber(that.telInput.val());
                });
            });
        },
        // adhere to the input's maxlength attr
        _cap: function(number) {
            var max = this.telInput.attr("maxlength");
            return max && number.length > max ? number.substr(0, max) : number;
        },
        // listen for mousedown, focus and blur
        _initFocusListeners: function() {
            var that = this;
            // mousedown decides where the cursor goes, so if we're focusing we must preventDefault as we'll be inserting the dial code, and we want the cursor to be at the end no matter where they click
            this.telInput.on("mousedown" + this.ns, function(e) {
                if (!that.telInput.is(":focus") && !that.telInput.val()) {
                    e.preventDefault();
                    // but this also cancels the focus, so we must trigger that manually
                    that.telInput.focus();
                }
            });
            // on focus: if empty, insert the dial code for the currently selected flag
            this.telInput.on("focus" + this.ns, function(e) {
                if (!that.telInput.val() && !that.telInput.prop("readonly") && that.selectedCountryData.dialCode) {
                    // insert the dial code
                    that.telInput.val("+" + that.selectedCountryData.dialCode);
                    // after auto-inserting a dial code, if the first key they hit is '+' then assume they are entering a new number, so remove the dial code. use keypress instead of keydown because keydown gets triggered for the shift key (required to hit the + key), and instead of keyup because that shows the new '+' before removing the old one
                    that.telInput.one("keypress.plus" + that.ns, function(e) {
                        if (e.which == keys.PLUS) {
                            that.telInput.val("");
                        }
                    });
                    // after tabbing in, make sure the cursor is at the end we must use setTimeout to get outside of the focus handler as it seems the selection happens after that
                    setTimeout(function() {
                        var input = that.telInput[0];
                        if (that.isGoodBrowser) {
                            var len = that.telInput.val().length;
                            input.setSelectionRange(len, len);
                        }
                    });
                }
            });
            // on blur or form submit: if just a dial code then remove it
            var form = this.telInput.prop("form");
            if (form) {
                $(form).on("submit" + this.ns, function() {
                    that._removeEmptyDialCode();
                });
            }
            this.telInput.on("blur" + this.ns, function() {
                that._removeEmptyDialCode();
            });
        },
        _removeEmptyDialCode: function() {
            var value = this.telInput.val(), startsPlus = value.charAt(0) == "+";
            if (startsPlus) {
                var numeric = this._getNumeric(value);
                // if just a plus, or if just a dial code
                if (!numeric || this.selectedCountryData.dialCode == numeric) {
                    this.telInput.val("");
                }
            }
            // remove the keypress listener we added on focus
            this.telInput.off("keypress.plus" + this.ns);
        },
        // extract the numeric digits from the given string
        _getNumeric: function(s) {
            return s.replace(/\D/g, "");
        },
        // show the dropdown
        _showDropdown: function() {
            this._setDropdownPosition();
            // update highlighting and scroll to active list item
            var activeListItem = this.countryList.children(".active");
            if (activeListItem.length) {
                this._highlightListItem(activeListItem);
                this._scrollTo(activeListItem);
            }
            // bind all the dropdown-related listeners: mouseover, click, click-off, keydown
            this._bindDropdownListeners();
            // update the arrow
            this.selectedFlagInner.children(".iti-arrow").addClass("up");
        },
        // decide where to position dropdown (depends on position within viewport, and scroll)
        _setDropdownPosition: function() {
            var that = this;
            if (this.options.dropdownContainer) {
                this.dropdown.appendTo(this.options.dropdownContainer);
            }
            // show the menu and grab the dropdown height
            this.dropdownHeight = this.countryList.removeClass("hide").outerHeight();
            if (!this.isMobile) {
                var pos = this.telInput.offset(), inputTop = pos.top, windowTop = $(window).scrollTop(), // dropdownFitsBelow = (dropdownBottom < windowBottom)
                dropdownFitsBelow = inputTop + this.telInput.outerHeight() + this.dropdownHeight < windowTop + $(window).height(), dropdownFitsAbove = inputTop - this.dropdownHeight > windowTop;
                // by default, the dropdown will be below the input. If we want to position it above the input, we add the dropup class.
                this.countryList.toggleClass("dropup", !dropdownFitsBelow && dropdownFitsAbove);
                // if dropdownContainer is enabled, calculate postion
                if (this.options.dropdownContainer) {
                    // by default the dropdown will be directly over the input because it's not in the flow. If we want to position it below, we need to add some extra top value.
                    var extraTop = !dropdownFitsBelow && dropdownFitsAbove ? 0 : this.telInput.innerHeight();
                    // calculate placement
                    this.dropdown.css({
                        top: inputTop + extraTop,
                        left: pos.left
                    });
                    // close menu on window scroll
                    $(window).on("scroll" + this.ns, function() {
                        that._closeDropdown();
                    });
                }
            }
        },
        // we only bind dropdown listeners when the dropdown is open
        _bindDropdownListeners: function() {
            var that = this;
            // when mouse over a list item, just highlight that one
            // we add the class "highlight", so if they hit "enter" we know which one to select
            this.countryList.on("mouseover" + this.ns, ".country", function(e) {
                that._highlightListItem($(this));
            });
            // listen for country selection
            this.countryList.on("click" + this.ns, ".country", function(e) {
                that._selectListItem($(this));
            });
            // click off to close
            // (except when this initial opening click is bubbling up)
            // we cannot just stopPropagation as it may be needed to close another instance
            var isOpening = true;
            $("html").on("click" + this.ns, function(e) {
                if (!isOpening) {
                    that._closeDropdown();
                }
                isOpening = false;
            });
            // listen for up/down scrolling, enter to select, or letters to jump to country name.
            // use keydown as keypress doesn't fire for non-char keys and we want to catch if they
            // just hit down and hold it to scroll down (no keyup event).
            // listen on the document because that's where key events are triggered if no input has focus
            var query = "", queryTimer = null;
            $(document).on("keydown" + this.ns, function(e) {
                // prevent down key from scrolling the whole page,
                // and enter key from submitting a form etc
                e.preventDefault();
                if (e.which == keys.UP || e.which == keys.DOWN) {
                    // up and down to navigate
                    that._handleUpDownKey(e.which);
                } else if (e.which == keys.ENTER) {
                    // enter to select
                    that._handleEnterKey();
                } else if (e.which == keys.ESC) {
                    // esc to close
                    that._closeDropdown();
                } else if (e.which >= keys.A && e.which <= keys.Z || e.which == keys.SPACE) {
                    // upper case letters (note: keyup/keydown only return upper case letters)
                    // jump to countries that start with the query string
                    if (queryTimer) {
                        clearTimeout(queryTimer);
                    }
                    query += String.fromCharCode(e.which);
                    that._searchForCountry(query);
                    // if the timer hits 1 second, reset the query
                    queryTimer = setTimeout(function() {
                        query = "";
                    }, 1e3);
                }
            });
        },
        // highlight the next/prev item in the list (and ensure it is visible)
        _handleUpDownKey: function(key) {
            var current = this.countryList.children(".highlight").first();
            var next = key == keys.UP ? current.prev() : current.next();
            if (next.length) {
                // skip the divider
                if (next.hasClass("divider")) {
                    next = key == keys.UP ? next.prev() : next.next();
                }
                this._highlightListItem(next);
                this._scrollTo(next);
            }
        },
        // select the currently highlighted item
        _handleEnterKey: function() {
            var currentCountry = this.countryList.children(".highlight").first();
            if (currentCountry.length) {
                this._selectListItem(currentCountry);
            }
        },
        // find the first list item whose name starts with the query string
        _searchForCountry: function(query) {
            for (var i = 0; i < this.countries.length; i++) {
                if (this._startsWith(this.countries[i].name, query)) {
                    var listItem = this.countryList.children("[data-country-code=" + this.countries[i].iso2 + "]").not(".preferred");
                    // update highlighting and scroll
                    this._highlightListItem(listItem);
                    this._scrollTo(listItem, true);
                    break;
                }
            }
        },
        // check if (uppercase) string a starts with string b
        _startsWith: function(a, b) {
            return a.substr(0, b.length).toUpperCase() == b;
        },
        // update the input's value to the given val (format first if possible)
        // NOTE: this is called from _setInitialState, handleUtils and setNumber
        _updateValFromNumber: function(number, doFormat) {
            if (doFormat && window.intlTelInputUtils && this.selectedCountryData) {
                var format = !this.options.separateDialCode && (this.options.nationalMode || number.charAt(0) != "+") ? intlTelInputUtils.numberFormat.NATIONAL : intlTelInputUtils.numberFormat.INTERNATIONAL;
                number = intlTelInputUtils.formatNumber(number, this.selectedCountryData.iso2, format);
            }
            number = this._beforeSetNumber(number);
            this.telInput.val(number);
        },
        // check if need to select a new flag based on the given number
        // Note: called from _setInitialState, keyup handler, setNumber
        _updateFlagFromNumber: function(number, isInit) {
            // if we're in nationalMode and we already have US/Canada selected, make sure the number starts with a +1 so _getDialCode will be able to extract the area code
            // update: if we dont yet have selectedCountryData, but we're here (trying to update the flag from the number), that means we're initialising the plugin with a number that already has a dial code, so fine to ignore this bit
            if (number && this.options.nationalMode && this.selectedCountryData && this.selectedCountryData.dialCode == "1" && number.charAt(0) != "+") {
                if (number.charAt(0) != "1") {
                    number = "1" + number;
                }
                number = "+" + number;
            }
            // try and extract valid dial code from input
            var dialCode = this._getDialCode(number), countryCode = null;
            if (dialCode) {
                // check if one of the matching countries is already selected
                var countryCodes = this.countryCodes[this._getNumeric(dialCode)], alreadySelected = this.selectedCountryData && $.inArray(this.selectedCountryData.iso2, countryCodes) != -1;
                // if a matching country is not already selected (or this is an unknown NANP area code): choose the first in the list
                if (!alreadySelected || this._isUnknownNanp(number, dialCode)) {
                    // if using onlyCountries option, countryCodes[0] may be empty, so we must find the first non-empty index
                    for (var j = 0; j < countryCodes.length; j++) {
                        if (countryCodes[j]) {
                            countryCode = countryCodes[j];
                            break;
                        }
                    }
                }
            } else if (number.charAt(0) == "+" && this._getNumeric(number).length) {
                // invalid dial code, so empty
                // Note: use getNumeric here because the number has not been formatted yet, so could contain bad chars
                countryCode = "";
            } else if (!number || number == "+") {
                // empty, or just a plus, so default
                countryCode = this.defaultCountry;
            }
            if (countryCode !== null) {
                this._setFlag(countryCode, isInit);
            }
        },
        // check if the given number contains an unknown area code from the North American Numbering Plan i.e. the only dialCode that could be extracted was +1 (instead of say +1 702) and the actual number's length is >=4
        _isUnknownNanp: function(number, dialCode) {
            return dialCode == "+1" && this._getNumeric(number).length >= 4;
        },
        // remove highlighting from other list items and highlight the given item
        _highlightListItem: function(listItem) {
            this.countryListItems.removeClass("highlight");
            listItem.addClass("highlight");
        },
        // find the country data for the given country code
        // the ignoreOnlyCountriesOption is only used during init() while parsing the onlyCountries array
        _getCountryData: function(countryCode, ignoreOnlyCountriesOption, allowFail) {
            var countryList = ignoreOnlyCountriesOption ? allCountries : this.countries;
            for (var i = 0; i < countryList.length; i++) {
                if (countryList[i].iso2 == countryCode) {
                    return countryList[i];
                }
            }
            if (allowFail) {
                return null;
            } else {
                throw new Error("No country data for '" + countryCode + "'");
            }
        },
        // select the given flag, update the placeholder and the active list item
        // Note: called from _setInitialState, _updateFlagFromNumber, _selectListItem, setCountry
        _setFlag: function(countryCode, isInit) {
            var prevCountry = this.selectedCountryData && this.selectedCountryData.iso2 ? this.selectedCountryData : {};
            // do this first as it will throw an error and stop if countryCode is invalid
            this.selectedCountryData = countryCode ? this._getCountryData(countryCode, false, false) : {};
            // update the defaultCountry - we only need the iso2 from now on, so just store that
            if (this.selectedCountryData.iso2) {
                this.defaultCountry = this.selectedCountryData.iso2;
            }
            this.selectedFlagInner.attr("class", "iti-flag " + countryCode);
            // update the selected country's title attribute
            var title = countryCode ? this.selectedCountryData.name + ": +" + this.selectedCountryData.dialCode : "Unknown";
            this.selectedFlagInner.parent().attr("title", title);
            if (this.options.separateDialCode) {
                var dialCode = this.selectedCountryData.dialCode ? "+" + this.selectedCountryData.dialCode : "", parent = this.telInput.parent();
                if (prevCountry.dialCode) {
                    parent.removeClass("iti-sdc-" + (prevCountry.dialCode.length + 1));
                }
                if (dialCode) {
                    parent.addClass("iti-sdc-" + dialCode.length);
                }
                this.selectedDialCode.text(dialCode);
            }
            // and the input's placeholder
            this._updatePlaceholder();
            // update the active list item
            this.countryListItems.removeClass("active");
            if (countryCode) {
                this.countryListItems.find(".iti-flag." + countryCode).first().closest(".country").addClass("active");
            }
            // on change flag, trigger a custom event
            if (!isInit && prevCountry.iso2 !== countryCode) {
                this.telInput.trigger("countrychange", this.selectedCountryData);
            }
        },
        // update the input placeholder to an example number from the currently selected country
        _updatePlaceholder: function() {
            var shouldSetPlaceholder = this.options.autoPlaceholder === "aggressive" || !this.hadInitialPlaceholder && (this.options.autoPlaceholder === true || this.options.autoPlaceholder === "polite");
            if (window.intlTelInputUtils && shouldSetPlaceholder && this.selectedCountryData) {
                var numberType = intlTelInputUtils.numberType[this.options.numberType], placeholder = this.selectedCountryData.iso2 ? intlTelInputUtils.getExampleNumber(this.selectedCountryData.iso2, this.options.nationalMode, numberType) : "";
                placeholder = this._beforeSetNumber(placeholder);
                if (typeof this.options.customPlaceholder === "function") {
                    placeholder = this.options.customPlaceholder(placeholder, this.selectedCountryData);
                }
                this.telInput.attr("placeholder", placeholder);
            }
        },
        // called when the user selects a list item from the dropdown
        _selectListItem: function(listItem) {
            // update selected flag and active list item
            this._setFlag(listItem.attr("data-country-code"));
            this._closeDropdown();
            this._updateDialCode(listItem.attr("data-dial-code"), true);
            // focus the input
            this.telInput.focus();
            // put cursor at end - this fix is required for FF and IE11 (with nationalMode=false i.e. auto inserting dial code), who try to put the cursor at the beginning the first time
            if (this.isGoodBrowser) {
                var len = this.telInput.val().length;
                this.telInput[0].setSelectionRange(len, len);
            }
        },
        // close the dropdown and unbind any listeners
        _closeDropdown: function() {
            this.countryList.addClass("hide");
            // update the arrow
            this.selectedFlagInner.children(".iti-arrow").removeClass("up");
            // unbind key events
            $(document).off(this.ns);
            // unbind click-off-to-close
            $("html").off(this.ns);
            // unbind hover and click listeners
            this.countryList.off(this.ns);
            // remove menu from container
            if (this.options.dropdownContainer) {
                if (!this.isMobile) {
                    $(window).off("scroll" + this.ns);
                }
                this.dropdown.detach();
            }
        },
        // check if an element is visible within it's container, else scroll until it is
        _scrollTo: function(element, middle) {
            var container = this.countryList, containerHeight = container.height(), containerTop = container.offset().top, containerBottom = containerTop + containerHeight, elementHeight = element.outerHeight(), elementTop = element.offset().top, elementBottom = elementTop + elementHeight, newScrollTop = elementTop - containerTop + container.scrollTop(), middleOffset = containerHeight / 2 - elementHeight / 2;
            if (elementTop < containerTop) {
                // scroll up
                if (middle) {
                    newScrollTop -= middleOffset;
                }
                container.scrollTop(newScrollTop);
            } else if (elementBottom > containerBottom) {
                // scroll down
                if (middle) {
                    newScrollTop += middleOffset;
                }
                var heightDifference = containerHeight - elementHeight;
                container.scrollTop(newScrollTop - heightDifference);
            }
        },
        // replace any existing dial code with the new one
        // Note: called from _selectListItem and setCountry
        _updateDialCode: function(newDialCode, hasSelectedListItem) {
            var inputVal = this.telInput.val(), newNumber;
            // save having to pass this every time
            newDialCode = "+" + newDialCode;
            if (inputVal.charAt(0) == "+") {
                // there's a plus so we're dealing with a replacement (doesn't matter if nationalMode or not)
                var prevDialCode = this._getDialCode(inputVal);
                if (prevDialCode) {
                    // current number contains a valid dial code, so replace it
                    newNumber = inputVal.replace(prevDialCode, newDialCode);
                } else {
                    // current number contains an invalid dial code, so ditch it
                    // (no way to determine where the invalid dial code ends and the rest of the number begins)
                    newNumber = newDialCode;
                }
            } else if (this.options.nationalMode || this.options.separateDialCode) {
                // don't do anything
                return;
            } else {
                // nationalMode is disabled
                if (inputVal) {
                    // there is an existing value with no dial code: prefix the new dial code
                    newNumber = newDialCode + inputVal;
                } else if (hasSelectedListItem || !this.options.autoHideDialCode) {
                    // no existing value and either they've just selected a list item, or autoHideDialCode is disabled: insert new dial code
                    newNumber = newDialCode;
                } else {
                    return;
                }
            }
            this.telInput.val(newNumber);
        },
        // try and extract a valid international dial code from a full telephone number
        // Note: returns the raw string inc plus character and any whitespace/dots etc
        _getDialCode: function(number) {
            var dialCode = "";
            // only interested in international numbers (starting with a plus)
            if (number.charAt(0) == "+") {
                var numericChars = "";
                // iterate over chars
                for (var i = 0; i < number.length; i++) {
                    var c = number.charAt(i);
                    // if char is number
                    if ($.isNumeric(c)) {
                        numericChars += c;
                        // if current numericChars make a valid dial code
                        if (this.countryCodes[numericChars]) {
                            // store the actual raw string (useful for matching later)
                            dialCode = number.substr(0, i + 1);
                        }
                        // longest dial code is 4 chars
                        if (numericChars.length == 4) {
                            break;
                        }
                    }
                }
            }
            return dialCode;
        },
        // get the input val, adding the dial code if separateDialCode is enabled
        _getFullNumber: function() {
            var prefix = this.options.separateDialCode ? "+" + this.selectedCountryData.dialCode : "";
            return prefix + this.telInput.val();
        },
        // remove the dial code if separateDialCode is enabled
        _beforeSetNumber: function(number) {
            if (this.options.separateDialCode) {
                var dialCode = this._getDialCode(number);
                if (dialCode) {
                    // US dialCode is "+1", which is what we want
                    // CA dialCode is "+1 123", which is wrong - should be "+1" (as it has multiple area codes)
                    // AS dialCode is "+1 684", which is what we want
                    // Solution: if the country has area codes, then revert to just the dial code
                    if (this.selectedCountryData.areaCodes !== null) {
                        dialCode = "+" + this.selectedCountryData.dialCode;
                    }
                    // a lot of numbers will have a space separating the dial code and the main number, and some NANP numbers will have a hyphen e.g. +1 684-733-1234 - in both cases we want to get rid of it
                    // NOTE: don't just trim all non-numerics as may want to preserve an open parenthesis etc
                    var start = number[dialCode.length] === " " || number[dialCode.length] === "-" ? dialCode.length + 1 : dialCode.length;
                    number = number.substr(start);
                }
            }
            return this._cap(number);
        },
        /********************
   *  PUBLIC METHODS
   ********************/
        // this is called when the geoip call returns
        handleAutoCountry: function() {
            if (this.options.initialCountry === "auto") {
                // we must set this even if there is an initial val in the input: in case the initial val is invalid and they delete it - they should see their auto country
                this.defaultCountry = $.fn[pluginName].autoCountry;
                // if there's no initial value in the input, then update the flag
                if (!this.telInput.val()) {
                    this.setCountry(this.defaultCountry);
                }
                this.autoCountryDeferred.resolve();
            }
        },
        // remove plugin
        destroy: function() {
            if (this.allowDropdown) {
                // make sure the dropdown is closed (and unbind listeners)
                this._closeDropdown();
                // click event to open dropdown
                this.selectedFlagInner.parent().off(this.ns);
                // label click hack
                this.telInput.closest("label").off(this.ns);
            }
            // unbind submit event handler on form
            if (this.options.autoHideDialCode) {
                var form = this.telInput.prop("form");
                if (form) {
                    $(form).off(this.ns);
                }
            }
            // unbind all events: key events, and focus/blur events if autoHideDialCode=true
            this.telInput.off(this.ns);
            // remove markup (but leave the original input)
            var container = this.telInput.parent();
            container.before(this.telInput).remove();
        },
        // get the extension from the current number
        getExtension: function() {
            if (window.intlTelInputUtils) {
                return intlTelInputUtils.getExtension(this._getFullNumber(), this.selectedCountryData.iso2);
            }
            return "";
        },
        // format the number to the given format
        getNumber: function(format) {
            if (window.intlTelInputUtils) {
                return intlTelInputUtils.formatNumber(this._getFullNumber(), this.selectedCountryData.iso2, format);
            }
            return "";
        },
        // get the type of the entered number e.g. landline/mobile
        getNumberType: function() {
            if (window.intlTelInputUtils) {
                return intlTelInputUtils.getNumberType(this._getFullNumber(), this.selectedCountryData.iso2);
            }
            return -99;
        },
        // get the country data for the currently selected flag
        getSelectedCountryData: function() {
            // if this is undefined, the plugin will return it's instance instead, so in that case an empty object makes more sense
            return this.selectedCountryData || {};
        },
        // get the validation error
        getValidationError: function() {
            if (window.intlTelInputUtils) {
                return intlTelInputUtils.getValidationError(this._getFullNumber(), this.selectedCountryData.iso2);
            }
            return -99;
        },
        // validate the input val - assumes the global function isValidNumber (from utilsScript)
        isValidNumber: function() {
            var val = $.trim(this._getFullNumber()), countryCode = this.options.nationalMode ? this.selectedCountryData.iso2 : "";
            return window.intlTelInputUtils ? intlTelInputUtils.isValidNumber(val, countryCode) : null;
        },
        // update the selected flag, and update the input val accordingly
        setCountry: function(countryCode) {
            countryCode = countryCode.toLowerCase();
            // check if already selected
            if (!this.selectedFlagInner.hasClass(countryCode)) {
                this._setFlag(countryCode);
                this._updateDialCode(this.selectedCountryData.dialCode, false);
            }
        },
        // set the input value and update the flag
        // NOTE: preventFormat arg is for public method
        setNumber: function(number, preventFormat) {
            // we must update the flag first, which updates this.selectedCountryData, which is used for formatting the number before displaying it
            this._updateFlagFromNumber(number);
            this._updateValFromNumber(number, !preventFormat);
        },
        // this is called when the utils request completes
        handleUtils: function() {
            // if the request was successful
            if (window.intlTelInputUtils) {
                // if there's an initial value in the input, then format it
                if (this.telInput.val()) {
                    this._updateValFromNumber(this.telInput.val(), this.options.formatOnInit);
                }
                this._updatePlaceholder();
            }
            this.utilsScriptDeferred.resolve();
        }
    };
    // using https://github.com/jquery-boilerplate/jquery-boilerplate/wiki/Extending-jQuery-Boilerplate
    // (adapted to allow public functions)
    $.fn[pluginName] = function(options) {
        var args = arguments;
        // Is the first parameter an object (options), or was omitted,
        // instantiate a new instance of the plugin.
        if (options === undefined || typeof options === "object") {
            // collect all of the deferred objects for all instances created with this selector
            var deferreds = [];
            this.each(function() {
                if (!$.data(this, "plugin_" + pluginName)) {
                    var instance = new Plugin(this, options);
                    var instanceDeferreds = instance._init();
                    // we now have 2 deffereds: 1 for auto country, 1 for utils script
                    deferreds.push(instanceDeferreds[0]);
                    deferreds.push(instanceDeferreds[1]);
                    $.data(this, "plugin_" + pluginName, instance);
                }
            });
            // return the promise from the "master" deferred object that tracks all the others
            return $.when.apply(null, deferreds);
        } else if (typeof options === "string" && options[0] !== "_") {
            // If the first parameter is a string and it doesn't start
            // with an underscore or "contains" the `init`-function,
            // treat this as a call to a public method.
            // Cache the method call to make it possible to return a value
            var returns;
            this.each(function() {
                var instance = $.data(this, "plugin_" + pluginName);
                // Tests that there's already a plugin-instance
                // and checks that the requested public method exists
                if (instance instanceof Plugin && typeof instance[options] === "function") {
                    // Call the method of our plugin instance,
                    // and pass it the supplied arguments.
                    returns = instance[options].apply(instance, Array.prototype.slice.call(args, 1));
                }
                // Allow instances to be destroyed via the 'destroy' method
                if (options === "destroy") {
                    $.data(this, "plugin_" + pluginName, null);
                }
            });
            // If the earlier cached method gives a value back return the value,
            // otherwise return this to preserve chainability.
            return returns !== undefined ? returns : this;
        }
    };
    /********************
 *  STATIC METHODS
 ********************/
    // get the country data object
    $.fn[pluginName].getCountryData = function() {
        return allCountries;
    };
    // load the utils script
    $.fn[pluginName].loadUtils = function(path, utilsScriptDeferred) {
        if (!$.fn[pluginName].loadedUtilsScript) {
            // don't do this twice! (dont just check if window.intlTelInputUtils exists as if init plugin multiple times in quick succession, it may not have finished loading yet)
            $.fn[pluginName].loadedUtilsScript = true;
            // dont use $.getScript as it prevents caching
            $.ajax({
                type: "GET",
                url: path,
                complete: function() {
                    // tell all instances that the utils request is complete
                    $(".intl-tel-input input").intlTelInput("handleUtils");
                },
                dataType: "script",
                cache: true
            });
        } else if (utilsScriptDeferred) {
            utilsScriptDeferred.resolve();
        }
    };
    // version
    $.fn[pluginName].version = "9.2.4";
    // default options
    $.fn[pluginName].defaults = defaults;
    // Array of country objects for the flag dropdown.
    // Each contains a name, country code (ISO 3166-1 alpha-2) and dial code.
    // Originally from https://github.com/mledoze/countries
    // then with a couple of manual re-arrangements to be alphabetical
    // then changed Kazakhstan from +76 to +7
    // and Vatican City from +379 to +39 (see issue 50)
    // and Caribean Netherlands from +5997 to +599
    // and Curacao from +5999 to +599
    // Removed:  Kosovo, Pitcairn Islands, South Georgia
    // UPDATE Sept 12th 2015
    // List of regions that have iso2 country codes, which I have chosen to omit:
    // (based on this information: https://en.wikipedia.org/wiki/List_of_country_calling_codes)
    // AQ - Antarctica - all different country codes depending on which "base"
    // BV - Bouvet Island - no calling code
    // GS - South Georgia and the South Sandwich Islands - "inhospitable collection of islands" - same flag and calling code as Falkland Islands
    // HM - Heard Island and McDonald Islands - no calling code
    // PN - Pitcairn - tiny population (56), same calling code as New Zealand
    // TF - French Southern Territories - no calling code
    // UM - United States Minor Outlying Islands - no calling code
    // UPDATE the criteria of supported countries or territories (see issue 297)
    // Have an iso2 code: https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
    // Have a country calling code: https://en.wikipedia.org/wiki/List_of_country_calling_codes
    // Have a flag
    // Must be supported by libphonenumber: https://github.com/googlei18n/libphonenumber
    // Update: converted objects to arrays to save bytes!
    // Update: added "priority" for countries with the same dialCode as others
    // Update: added array of area codes for countries with the same dialCode as others
    // So each country array has the following information:
    // [
    //    Country name,
    //    iso2 code,
    //    International dial code,
    //    Order (if >1 country with same dial code),
    //    Area codes (if >1 country with same dial code)
    // ]
    var allCountries = [ [ "Afghanistan (‫افغانستان‬‎)", "af", "93" ], [ "Albania (Shqipëri)", "al", "355" ], [ "Algeria (‫الجزائر‬‎)", "dz", "213" ], [ "American Samoa", "as", "1684" ], [ "Andorra", "ad", "376" ], [ "Angola", "ao", "244" ], [ "Anguilla", "ai", "1264" ], [ "Antigua and Barbuda", "ag", "1268" ], [ "Argentina", "ar", "54" ], [ "Armenia (Հայաստան)", "am", "374" ], [ "Aruba", "aw", "297" ], [ "Australia", "au", "61", 0 ], [ "Austria (Österreich)", "at", "43" ], [ "Azerbaijan (Azərbaycan)", "az", "994" ], [ "Bahamas", "bs", "1242" ], [ "Bahrain (‫البحرين‬‎)", "bh", "973" ], [ "Bangladesh (বাংলাদেশ)", "bd", "880" ], [ "Barbados", "bb", "1246" ], [ "Belarus (Беларусь)", "by", "375" ], [ "Belgium (België)", "be", "32" ], [ "Belize", "bz", "501" ], [ "Benin (Bénin)", "bj", "229" ], [ "Bermuda", "bm", "1441" ], [ "Bhutan (འབྲུག)", "bt", "975" ], [ "Bolivia", "bo", "591" ], [ "Bosnia and Herzegovina (Босна и Херцеговина)", "ba", "387" ], [ "Botswana", "bw", "267" ], [ "Brazil (Brasil)", "br", "55" ], [ "British Indian Ocean Territory", "io", "246" ], [ "British Virgin Islands", "vg", "1284" ], [ "Brunei", "bn", "673" ], [ "Bulgaria (България)", "bg", "359" ], [ "Burkina Faso", "bf", "226" ], [ "Burundi (Uburundi)", "bi", "257" ], [ "Cambodia (កម្ពុជា)", "kh", "855" ], [ "Cameroon (Cameroun)", "cm", "237" ], [ "Canada", "ca", "1", 1, [ "204", "226", "236", "249", "250", "289", "306", "343", "365", "387", "403", "416", "418", "431", "437", "438", "450", "506", "514", "519", "548", "579", "581", "587", "604", "613", "639", "647", "672", "705", "709", "742", "778", "780", "782", "807", "819", "825", "867", "873", "902", "905" ] ], [ "Cape Verde (Kabu Verdi)", "cv", "238" ], [ "Caribbean Netherlands", "bq", "599", 1 ], [ "Cayman Islands", "ky", "1345" ], [ "Central African Republic (République centrafricaine)", "cf", "236" ], [ "Chad (Tchad)", "td", "235" ], [ "Chile", "cl", "56" ], [ "China (中国)", "cn", "86" ], [ "Christmas Island", "cx", "61", 2 ], [ "Cocos (Keeling) Islands", "cc", "61", 1 ], [ "Colombia", "co", "57" ], [ "Comoros (‫جزر القمر‬‎)", "km", "269" ], [ "Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)", "cd", "243" ], [ "Congo (Republic) (Congo-Brazzaville)", "cg", "242" ], [ "Cook Islands", "ck", "682" ], [ "Costa Rica", "cr", "506" ], [ "Côte d’Ivoire", "ci", "225" ], [ "Croatia (Hrvatska)", "hr", "385" ], [ "Cuba", "cu", "53" ], [ "Curaçao", "cw", "599", 0 ], [ "Cyprus (Κύπρος)", "cy", "357" ], [ "Czech Republic (Česká republika)", "cz", "420" ], [ "Denmark (Danmark)", "dk", "45" ], [ "Djibouti", "dj", "253" ], [ "Dominica", "dm", "1767" ], [ "Dominican Republic (República Dominicana)", "do", "1", 2, [ "809", "829", "849" ] ], [ "Ecuador", "ec", "593" ], [ "Egypt (‫مصر‬‎)", "eg", "20" ], [ "El Salvador", "sv", "503" ], [ "Equatorial Guinea (Guinea Ecuatorial)", "gq", "240" ], [ "Eritrea", "er", "291" ], [ "Estonia (Eesti)", "ee", "372" ], [ "Ethiopia", "et", "251" ], [ "Falkland Islands (Islas Malvinas)", "fk", "500" ], [ "Faroe Islands (Føroyar)", "fo", "298" ], [ "Fiji", "fj", "679" ], [ "Finland (Suomi)", "fi", "358", 0 ], [ "France", "fr", "33" ], [ "French Guiana (Guyane française)", "gf", "594" ], [ "French Polynesia (Polynésie française)", "pf", "689" ], [ "Gabon", "ga", "241" ], [ "Gambia", "gm", "220" ], [ "Georgia (საქართველო)", "ge", "995" ], [ "Germany (Deutschland)", "de", "49" ], [ "Ghana (Gaana)", "gh", "233" ], [ "Gibraltar", "gi", "350" ], [ "Greece (Ελλάδα)", "gr", "30" ], [ "Greenland (Kalaallit Nunaat)", "gl", "299" ], [ "Grenada", "gd", "1473" ], [ "Guadeloupe", "gp", "590", 0 ], [ "Guam", "gu", "1671" ], [ "Guatemala", "gt", "502" ], [ "Guernsey", "gg", "44", 1 ], [ "Guinea (Guinée)", "gn", "224" ], [ "Guinea-Bissau (Guiné Bissau)", "gw", "245" ], [ "Guyana", "gy", "592" ], [ "Haiti", "ht", "509" ], [ "Honduras", "hn", "504" ], [ "Hong Kong (香港)", "hk", "852" ], [ "Hungary (Magyarország)", "hu", "36" ], [ "Iceland (Ísland)", "is", "354" ], [ "India (भारत)", "in", "91" ], [ "Indonesia", "id", "62" ], [ "Iran (‫ایران‬‎)", "ir", "98" ], [ "Iraq (‫العراق‬‎)", "iq", "964" ], [ "Ireland", "ie", "353" ], [ "Isle of Man", "im", "44", 2 ], [ "Israel (‫ישראל‬‎)", "il", "972" ], [ "Italy (Italia)", "it", "39", 0 ], [ "Jamaica", "jm", "1876" ], [ "Japan (日本)", "jp", "81" ], [ "Jersey", "je", "44", 3 ], [ "Jordan (‫الأردن‬‎)", "jo", "962" ], [ "Kazakhstan (Казахстан)", "kz", "7", 1 ], [ "Kenya", "ke", "254" ], [ "Kiribati", "ki", "686" ], [ "Kosovo", "xk", "383" ], [ "Kuwait (‫الكويت‬‎)", "kw", "965" ], [ "Kyrgyzstan (Кыргызстан)", "kg", "996" ], [ "Laos (ລາວ)", "la", "856" ], [ "Latvia (Latvija)", "lv", "371" ], [ "Lebanon (‫لبنان‬‎)", "lb", "961" ], [ "Lesotho", "ls", "266" ], [ "Liberia", "lr", "231" ], [ "Libya (‫ليبيا‬‎)", "ly", "218" ], [ "Liechtenstein", "li", "423" ], [ "Lithuania (Lietuva)", "lt", "370" ], [ "Luxembourg", "lu", "352" ], [ "Macau (澳門)", "mo", "853" ], [ "Macedonia (FYROM) (Македонија)", "mk", "389" ], [ "Madagascar (Madagasikara)", "mg", "261" ], [ "Malawi", "mw", "265" ], [ "Malaysia", "my", "60" ], [ "Maldives", "mv", "960" ], [ "Mali", "ml", "223" ], [ "Malta", "mt", "356" ], [ "Marshall Islands", "mh", "692" ], [ "Martinique", "mq", "596" ], [ "Mauritania (‫موريتانيا‬‎)", "mr", "222" ], [ "Mauritius (Moris)", "mu", "230" ], [ "Mayotte", "yt", "262", 1 ], [ "Mexico (México)", "mx", "52" ], [ "Micronesia", "fm", "691" ], [ "Moldova (Republica Moldova)", "md", "373" ], [ "Monaco", "mc", "377" ], [ "Mongolia (Монгол)", "mn", "976" ], [ "Montenegro (Crna Gora)", "me", "382" ], [ "Montserrat", "ms", "1664" ], [ "Morocco (‫المغرب‬‎)", "ma", "212", 0 ], [ "Mozambique (Moçambique)", "mz", "258" ], [ "Myanmar (Burma) (မြန်မာ)", "mm", "95" ], [ "Namibia (Namibië)", "na", "264" ], [ "Nauru", "nr", "674" ], [ "Nepal (नेपाल)", "np", "977" ], [ "Netherlands (Nederland)", "nl", "31" ], [ "New Caledonia (Nouvelle-Calédonie)", "nc", "687" ], [ "New Zealand", "nz", "64" ], [ "Nicaragua", "ni", "505" ], [ "Niger (Nijar)", "ne", "227" ], [ "Nigeria", "ng", "234" ], [ "Niue", "nu", "683" ], [ "Norfolk Island", "nf", "672" ], [ "North Korea (조선 민주주의 인민 공화국)", "kp", "850" ], [ "Northern Mariana Islands", "mp", "1670" ], [ "Norway (Norge)", "no", "47", 0 ], [ "Oman (‫عُمان‬‎)", "om", "968" ], [ "Pakistan (‫پاکستان‬‎)", "pk", "92" ], [ "Palau", "pw", "680" ], [ "Palestine (‫فلسطين‬‎)", "ps", "970" ], [ "Panama (Panamá)", "pa", "507" ], [ "Papua New Guinea", "pg", "675" ], [ "Paraguay", "py", "595" ], [ "Peru (Perú)", "pe", "51" ], [ "Philippines", "ph", "63" ], [ "Poland (Polska)", "pl", "48" ], [ "Portugal", "pt", "351" ], [ "Puerto Rico", "pr", "1", 3, [ "787", "939" ] ], [ "Qatar (‫قطر‬‎)", "qa", "974" ], [ "Réunion (La Réunion)", "re", "262", 0 ], [ "Romania (România)", "ro", "40" ], [ "Russia (Россия)", "ru", "7", 0 ], [ "Rwanda", "rw", "250" ], [ "Saint Barthélemy (Saint-Barthélemy)", "bl", "590", 1 ], [ "Saint Helena", "sh", "290" ], [ "Saint Kitts and Nevis", "kn", "1869" ], [ "Saint Lucia", "lc", "1758" ], [ "Saint Martin (Saint-Martin (partie française))", "mf", "590", 2 ], [ "Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)", "pm", "508" ], [ "Saint Vincent and the Grenadines", "vc", "1784" ], [ "Samoa", "ws", "685" ], [ "San Marino", "sm", "378" ], [ "São Tomé and Príncipe (São Tomé e Príncipe)", "st", "239" ], [ "Saudi Arabia (‫المملكة العربية السعودية‬‎)", "sa", "966" ], [ "Senegal (Sénégal)", "sn", "221" ], [ "Serbia (Србија)", "rs", "381" ], [ "Seychelles", "sc", "248" ], [ "Sierra Leone", "sl", "232" ], [ "Singapore", "sg", "65" ], [ "Sint Maarten", "sx", "1721" ], [ "Slovakia (Slovensko)", "sk", "421" ], [ "Slovenia (Slovenija)", "si", "386" ], [ "Solomon Islands", "sb", "677" ], [ "Somalia (Soomaaliya)", "so", "252" ], [ "South Africa", "za", "27" ], [ "South Korea (대한민국)", "kr", "82" ], [ "South Sudan (‫جنوب السودان‬‎)", "ss", "211" ], [ "Spain (España)", "es", "34" ], [ "Sri Lanka (ශ්‍රී ලංකාව)", "lk", "94" ], [ "Sudan (‫السودان‬‎)", "sd", "249" ], [ "Suriname", "sr", "597" ], [ "Svalbard and Jan Mayen", "sj", "47", 1 ], [ "Swaziland", "sz", "268" ], [ "Sweden (Sverige)", "se", "46" ], [ "Switzerland (Schweiz)", "ch", "41" ], [ "Syria (‫سوريا‬‎)", "sy", "963" ], [ "Taiwan (台灣)", "tw", "886" ], [ "Tajikistan", "tj", "992" ], [ "Tanzania", "tz", "255" ], [ "Thailand (ไทย)", "th", "66" ], [ "Timor-Leste", "tl", "670" ], [ "Togo", "tg", "228" ], [ "Tokelau", "tk", "690" ], [ "Tonga", "to", "676" ], [ "Trinidad and Tobago", "tt", "1868" ], [ "Tunisia (‫تونس‬‎)", "tn", "216" ], [ "Turkey (Türkiye)", "tr", "90" ], [ "Turkmenistan", "tm", "993" ], [ "Turks and Caicos Islands", "tc", "1649" ], [ "Tuvalu", "tv", "688" ], [ "U.S. Virgin Islands", "vi", "1340" ], [ "Uganda", "ug", "256" ], [ "Ukraine (Україна)", "ua", "380" ], [ "United Arab Emirates (‫الإمارات العربية المتحدة‬‎)", "ae", "971" ], [ "United Kingdom", "gb", "44", 0 ], [ "United States", "us", "1", 0 ], [ "Uruguay", "uy", "598" ], [ "Uzbekistan (Oʻzbekiston)", "uz", "998" ], [ "Vanuatu", "vu", "678" ], [ "Vatican City (Città del Vaticano)", "va", "39", 1 ], [ "Venezuela", "ve", "58" ], [ "Vietnam (Việt Nam)", "vn", "84" ], [ "Wallis and Futuna", "wf", "681" ], [ "Western Sahara (‫الصحراء الغربية‬‎)", "eh", "212", 1 ], [ "Yemen (‫اليمن‬‎)", "ye", "967" ], [ "Zambia", "zm", "260" ], [ "Zimbabwe", "zw", "263" ], [ "Åland Islands", "ax", "358", 1 ] ];
    // loop over all of the countries above
    for (var i = 0; i < allCountries.length; i++) {
        var c = allCountries[i];
        allCountries[i] = {
            name: c[0],
            iso2: c[1],
            dialCode: c[2],
            priority: c[3] || 0,
            areaCodes: c[4] || null
        };
    }
});
(function(){for(var aa="function"==typeof Object.defineProperties?Object.defineProperty:function(a,b,c){if(c.get||c.set)throw new TypeError("ES3 does not support getters and setters.");a!=Array.prototype&&a!=Object.prototype&&(a[b]=c.value)},k="undefined"!=typeof window&&window===this?this:"undefined"!=typeof global&&null!=global?global:this,m=["String","prototype","repeat"],n=0;n<m.length-1;n++){var p=m[n];p in k||(k[p]={});k=k[p]}
var ba=m[m.length-1],r=k[ba],t=r?r:function(a){var b;if(null==this)throw new TypeError("The 'this' value for String.prototype.repeat must not be null or undefined");b=this+"";if(0>a||1342177279<a)throw new RangeError("Invalid count value");a|=0;for(var c="";a;)if(a&1&&(c+=b),a>>>=1)b+=b;return c};t!=r&&null!=t&&aa(k,ba,{configurable:!0,writable:!0,value:t});var ca=this;function u(a){return"string"==typeof a}
function v(a,b){var c=a.split("."),d=ca;c[0]in d||!d.execScript||d.execScript("var "+c[0]);for(var e;c.length&&(e=c.shift());)c.length||void 0===b?d[e]?d=d[e]:d=d[e]={}:d[e]=b}function w(a,b){function c(){}c.prototype=b.prototype;a.$=b.prototype;a.prototype=new c;a.prototype.constructor=a;a.w=function(a,c,f){for(var d=Array(arguments.length-2),e=2;e<arguments.length;e++)d[e-2]=arguments[e];return b.prototype[c].apply(a,d)}};var x=Array.prototype.indexOf?function(a,b,c){return Array.prototype.indexOf.call(a,b,c)}:function(a,b,c){c=null==c?0:0>c?Math.max(0,a.length+c):c;if(u(a))return u(b)&&1==b.length?a.indexOf(b,c):-1;for(;c<a.length;c++)if(c in a&&a[c]===b)return c;return-1};function da(a,b){a.sort(b||ea)}function ea(a,b){return a>b?1:a<b?-1:0};function fa(a,b){this.a=a;this.h=!!b.i;this.c=b.b;this.m=b.type;this.l=!1;switch(this.c){case ga:case ha:case ia:case ja:case ka:case la:case ma:this.l=!0}this.g=b.defaultValue}var ma=1,la=2,ga=3,ha=4,ia=6,ja=16,ka=18;function na(a){var b=[],c=0,d;for(d in a)b[c++]=a[d];return b};function oa(a,b){this.c=a;this.a={};for(var c=0;c<b.length;c++){var d=b[c];this.a[d.a]=d}}function pa(a){a=na(a.a);da(a,function(a,c){return a.a-c.a});return a};function y(){this.a={};this.g=this.f().a;this.c=this.h=null}y.prototype.has=function(a){return null!=this.a[a.a]};y.prototype.get=function(a,b){return A(this,a.a,b)};y.prototype.set=function(a,b){B(this,a.a,b)};
function qa(a,b){for(var c=pa(a.f()),d=0;d<c.length;d++){var e=c[d],f=e.a;if(null!=b.a[f]){a.c&&delete a.c[e.a];var g=11==e.c||10==e.c;if(e.h)for(var e=C(b,f)||[],h=0;h<e.length;h++){var l=a,q=f,z=g?e[h].clone():e[h];l.a[q]||(l.a[q]=[]);l.a[q].push(z);l.c&&delete l.c[q]}else e=C(b,f),g?(g=C(a,f))?qa(g,e):B(a,f,e.clone()):B(a,f,e)}}}y.prototype.clone=function(){var a=new this.constructor;a!=this&&(a.a={},a.c&&(a.c={}),qa(a,this));return a};
function C(a,b){var c=a.a[b];if(null==c)return null;if(a.h){if(!(b in a.c)){var d=a.h,e=a.g[b];if(null!=c)if(e.h){for(var f=[],g=0;g<c.length;g++)f[g]=d.a(e,c[g]);c=f}else c=d.a(e,c);return a.c[b]=c}return a.c[b]}return c}function A(a,b,c){var d=C(a,b);return a.g[b].h?d[c||0]:d}function D(a,b){var c;if(null!=a.a[b])c=A(a,b,void 0);else a:{c=a.g[b];if(void 0===c.g){var d=c.m;if(d===Boolean)c.g=!1;else if(d===Number)c.g=0;else if(d===String)c.g=c.l?"0":"";else{c=new d;break a}}c=c.g}return c}
function ra(a,b){return a.g[b].h?null!=a.a[b]?a.a[b].length:0:null!=a.a[b]?1:0}function B(a,b,c){a.a[b]=c;a.c&&(a.c[b]=c)}function E(a,b){var c=[],d;for(d in b)0!=d&&c.push(new fa(d,b[d]));return new oa(a,c)};function F(){}F.prototype.c=function(a){new a.c;throw Error("Unimplemented");};F.prototype.a=function(a,b){if(11==a.c||10==a.c)return b instanceof y?b:this.c(a.m.prototype.f(),b);if(14==a.c){if(u(b)&&sa.test(b)){var c=Number(b);if(0<c)return c}return b}if(!a.l)return b;c=a.m;if(c===String){if("number"==typeof b)return String(b)}else if(c===Number&&u(b)&&("Infinity"===b||"-Infinity"===b||"NaN"===b||sa.test(b)))return Number(b);return b};var sa=/^-?[0-9]+$/;function G(){}w(G,F);G.prototype.c=function(a,b){var c=new a.c;c.h=this;c.a=b;c.c={};return c};function H(){}w(H,G);H.prototype.a=function(a,b){return 8==a.c?!!b:F.prototype.a.apply(this,arguments)};function I(a,b){null!=a&&this.a.apply(this,arguments)}I.prototype.c="";I.prototype.set=function(a){this.c=""+a};I.prototype.a=function(a,b,c){this.c+=String(a);if(null!=b)for(var d=1;d<arguments.length;d++)this.c+=arguments[d];return this};I.prototype.toString=function(){return this.c};/*

 Protocol Buffer 2 Copyright 2008 Google Inc.
 All other code copyright its respective owners.
 Copyright (C) 2010 The Libphonenumber Authors

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
*/
function J(){y.call(this)}w(J,y);var ta=null;function K(){y.call(this)}w(K,y);var ua=null;function L(){y.call(this)}w(L,y);var wa=null;
J.prototype.f=function(){var a=ta;a||(ta=a=E(J,{0:{name:"NumberFormat",j:"i18n.phonenumbers.NumberFormat"},1:{name:"pattern",required:!0,b:9,type:String},2:{name:"format",required:!0,b:9,type:String},3:{name:"leading_digits_pattern",i:!0,b:9,type:String},4:{name:"national_prefix_formatting_rule",b:9,type:String},6:{name:"national_prefix_optional_when_formatting",b:8,type:Boolean},5:{name:"domestic_carrier_code_formatting_rule",b:9,type:String}}));return a};J.f=J.prototype.f;
K.prototype.f=function(){var a=ua;a||(ua=a=E(K,{0:{name:"PhoneNumberDesc",j:"i18n.phonenumbers.PhoneNumberDesc"},2:{name:"national_number_pattern",b:9,type:String},3:{name:"possible_number_pattern",b:9,type:String},9:{name:"possible_length",i:!0,b:5,type:Number},10:{name:"possible_length_local_only",i:!0,b:5,type:Number},6:{name:"example_number",b:9,type:String},7:{name:"national_number_matcher_data",b:12,type:String},8:{name:"possible_number_matcher_data",b:12,type:String}}));return a};K.f=K.prototype.f;
L.prototype.f=function(){var a=wa;a||(wa=a=E(L,{0:{name:"PhoneMetadata",j:"i18n.phonenumbers.PhoneMetadata"},1:{name:"general_desc",b:11,type:K},2:{name:"fixed_line",b:11,type:K},3:{name:"mobile",b:11,type:K},4:{name:"toll_free",b:11,type:K},5:{name:"premium_rate",b:11,type:K},6:{name:"shared_cost",b:11,type:K},7:{name:"personal_number",b:11,type:K},8:{name:"voip",b:11,type:K},21:{name:"pager",b:11,type:K},25:{name:"uan",b:11,type:K},27:{name:"emergency",b:11,type:K},28:{name:"voicemail",b:11,type:K},
24:{name:"no_international_dialling",b:11,type:K},9:{name:"id",required:!0,b:9,type:String},10:{name:"country_code",b:5,type:Number},11:{name:"international_prefix",b:9,type:String},17:{name:"preferred_international_prefix",b:9,type:String},12:{name:"national_prefix",b:9,type:String},13:{name:"preferred_extn_prefix",b:9,type:String},15:{name:"national_prefix_for_parsing",b:9,type:String},16:{name:"national_prefix_transform_rule",b:9,type:String},18:{name:"same_mobile_and_fixed_line_pattern",b:8,defaultValue:!1,
type:Boolean},19:{name:"number_format",i:!0,b:11,type:J},20:{name:"intl_number_format",i:!0,b:11,type:J},22:{name:"main_country_for_code",b:8,defaultValue:!1,type:Boolean},23:{name:"leading_digits",b:9,type:String},26:{name:"leading_zero_possible",b:8,defaultValue:!1,type:Boolean}}));return a};L.f=L.prototype.f;function M(){y.call(this)}var N;w(M,y);var xa={v:1,u:5,s:10,o:20};
M.prototype.f=function(){N||(N=E(M,{0:{name:"PhoneNumber",j:"i18n.phonenumbers.PhoneNumber"},1:{name:"country_code",required:!0,b:5,type:Number},2:{name:"national_number",required:!0,b:4,type:Number},3:{name:"extension",b:9,type:String},4:{name:"italian_leading_zero",b:8,type:Boolean},8:{name:"number_of_leading_zeros",b:5,defaultValue:1,type:Number},5:{name:"raw_input",b:9,type:String},6:{name:"country_code_source",b:14,defaultValue:1,type:xa},7:{name:"preferred_domestic_carrier_code",b:9,type:String}}));
return N};M.ctor=M;M.ctor.f=M.prototype.f;/*

 Copyright (C) 2010 The Libphonenumber Authors

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
*/
var O={1:"US AG AI AS BB BM BS CA DM DO GD GU JM KN KY LC MP MS PR SX TC TT VC VG VI".split(" "),7:["RU","KZ"],20:["EG"],27:["ZA"],30:["GR"],31:["NL"],32:["BE"],33:["FR"],34:["ES"],36:["HU"],39:["IT","VA"],40:["RO"],41:["CH"],43:["AT"],44:["GB","GG","IM","JE"],45:["DK"],46:["SE"],47:["NO","SJ"],48:["PL"],49:["DE"],51:["PE"],52:["MX"],53:["CU"],54:["AR"],55:["BR"],56:["CL"],57:["CO"],58:["VE"],60:["MY"],61:["AU","CC","CX"],62:["ID"],63:["PH"],64:["NZ"],65:["SG"],66:["TH"],81:["JP"],82:["KR"],84:["VN"],
86:["CN"],90:["TR"],91:["IN"],92:["PK"],93:["AF"],94:["LK"],95:["MM"],98:["IR"],211:["SS"],212:["MA","EH"],213:["DZ"],216:["TN"],218:["LY"],220:["GM"],221:["SN"],222:["MR"],223:["ML"],224:["GN"],225:["CI"],226:["BF"],227:["NE"],228:["TG"],229:["BJ"],230:["MU"],231:["LR"],232:["SL"],233:["GH"],234:["NG"],235:["TD"],236:["CF"],237:["CM"],238:["CV"],239:["ST"],240:["GQ"],241:["GA"],242:["CG"],243:["CD"],244:["AO"],245:["GW"],246:["IO"],247:["AC"],248:["SC"],249:["SD"],250:["RW"],251:["ET"],252:["SO"],
253:["DJ"],254:["KE"],255:["TZ"],256:["UG"],257:["BI"],258:["MZ"],260:["ZM"],261:["MG"],262:["RE","YT"],263:["ZW"],264:["NA"],265:["MW"],266:["LS"],267:["BW"],268:["SZ"],269:["KM"],290:["SH","TA"],291:["ER"],297:["AW"],298:["FO"],299:["GL"],350:["GI"],351:["PT"],352:["LU"],353:["IE"],354:["IS"],355:["AL"],356:["MT"],357:["CY"],358:["FI","AX"],359:["BG"],370:["LT"],371:["LV"],372:["EE"],373:["MD"],374:["AM"],375:["BY"],376:["AD"],377:["MC"],378:["SM"],380:["UA"],381:["RS"],382:["ME"],385:["HR"],386:["SI"],
387:["BA"],389:["MK"],420:["CZ"],421:["SK"],423:["LI"],500:["FK"],501:["BZ"],502:["GT"],503:["SV"],504:["HN"],505:["NI"],506:["CR"],507:["PA"],508:["PM"],509:["HT"],590:["GP","BL","MF"],591:["BO"],592:["GY"],593:["EC"],594:["GF"],595:["PY"],596:["MQ"],597:["SR"],598:["UY"],599:["CW","BQ"],670:["TL"],672:["NF"],673:["BN"],674:["NR"],675:["PG"],676:["TO"],677:["SB"],678:["VU"],679:["FJ"],680:["PW"],681:["WF"],682:["CK"],683:["NU"],685:["WS"],686:["KI"],687:["NC"],688:["TV"],689:["PF"],690:["TK"],691:["FM"],
692:["MH"],800:["001"],808:["001"],850:["KP"],852:["HK"],853:["MO"],855:["KH"],856:["LA"],870:["001"],878:["001"],880:["BD"],881:["001"],882:["001"],883:["001"],886:["TW"],888:["001"],960:["MV"],961:["LB"],962:["JO"],963:["SY"],964:["IQ"],965:["KW"],966:["SA"],967:["YE"],968:["OM"],970:["PS"],971:["AE"],972:["IL"],973:["BH"],974:["QA"],975:["BT"],976:["MN"],977:["NP"],979:["001"],992:["TJ"],993:["TM"],994:["AZ"],995:["GE"],996:["KG"],998:["UZ"]},ya={AC:[,[,,"[46]\\d{4}|[01589]\\d{5}","\\d{5,6}",,
,,,,[5,6]],[,,"6[2-467]\\d{3}","\\d{5}",,,"62889",,,[5]],[,,"4\\d{4}","\\d{5}",,,"40123",,,[5]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"AC",247,"00",,,,,,,,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"[01589]\\d{5}","\\d{6}",,,"542011",,,[6]],,,[,,"NA","NA",,,,,,[-1]]],AD:[,[,,"(?:[346-9]|180)\\d{5}","\\d{6,8}",,,,,,[6,8]],[,,"[78]\\d{5}","\\d{6}",,,"712345",,,[6]],[,,"[346]\\d{5}","\\d{6}",,,"312345",,,[6]],
[,,"180[02]\\d{4}","\\d{8}",,,"18001234",,,[8]],[,,"9\\d{5}","\\d{6}",,,"912345",,,[6]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"AD",376,"00",,,,,,,,[[,"(\\d{3})(\\d{3})","$1 $2",["[346-9]"]],[,"(180[02])(\\d{4})","$1 $2",["1"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],AE:[,[,,"[2-79]\\d{7,8}|800\\d{2,9}","\\d{5,12}",,,,,,[5,6,7,8,9,10,11,12]],[,,"[2-4679][2-8]\\d{6}","\\d{7,8}",,,"22345678",,,[8]],[,,"5[024-6]\\d{7}",
"\\d{9}",,,"501234567",,,[9]],[,,"400\\d{6}|800\\d{2,9}","\\d{5,12}",,,"800123456"],[,,"900[02]\\d{5}","\\d{9}",,,"900234567",,,[9]],[,,"700[05]\\d{5}","\\d{9}",,,"700012345",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"AE",971,"00","0",,,"0",,,,[[,"([2-4679])(\\d{3})(\\d{4})","$1 $2 $3",["[2-4679][2-8]"],"0$1"],[,"(5\\d)(\\d{3})(\\d{4})","$1 $2 $3",["5"],"0$1"],[,"([479]00)(\\d)(\\d{5})","$1 $2 $3",["[479]0"],"$1"],[,"([68]00)(\\d{2,9})","$1 $2",["60|8"],"$1"]],,[,,"NA","NA",,,,,,[-1]],
,,[,,"NA","NA",,,,,,[-1]],[,,"600[25]\\d{5}","\\d{9}",,,"600212345",,,[9]],,,[,,"NA","NA",,,,,,[-1]]],AF:[,[,,"[2-7]\\d{8}","\\d{7,9}",,,,,,[9],[7]],[,,"(?:[25][0-8]|[34][0-4]|6[0-5])[2-9]\\d{6}","\\d{7,9}",,,"234567890"],[,,"7(?:[014-9]\\d{7}|2[89]\\d{6})","\\d{9}",,,"701234567"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"AF",93,"00","0",,,"0",,,,[[,"([2-7]\\d)(\\d{3})(\\d{4})","$1 $2 $3",["[2-7]"],"0$1"]],,[,,"NA","NA",
,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],AG:[,[,,"[2589]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"268(?:4(?:6[0-38]|84)|56[0-2])\\d{4}","\\d{7}(?:\\d{3})?",,,"2684601234"],[,,"268(?:464|7(?:2\\d|3[246]|64|7[0-689]|8[02-68]))\\d{4}","\\d{7}(?:\\d{3})?",,,"2684641234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",
,,"5002345678"],[,,"26848[01]\\d{4}","\\d{7}(?:\\d{3})?",,,"2684801234"],"AG",1,"011","1",,,"1",,,,,,[,,"26840[69]\\d{4}","\\d{7}(?:\\d{3})?",,,"2684061234"],,"268",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],AI:[,[,,"[2589]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"2644(?:6[12]|9[78])\\d{4}","\\d{7}(?:\\d{3})?",,,"2644612345"],[,,"264(?:235|476|5(?:3[6-9]|8[1-4])|7(?:29|72))\\d{4}","\\d{7}(?:\\d{3})?",,,"2642351234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",
,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"AI",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"264",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],AL:[,[,,"[2-57]\\d{7}|6\\d{8}|8\\d{5,7}|9\\d{5}","\\d{5,9}",,,,,,[6,7,8,9],[5]],[,,"(?:2(?:[168][1-9]|[247]\\d|9[1-7])|3(?:1[1-3]|[2-6]\\d|[79][1-8]|8[1-9])|4\\d{2}|5(?:1[1-4]|[2-578]\\d|6[1-5]|9[1-7])|8(?:[19][1-5]|[2-6]\\d|[78][1-7]))\\d{5}",
"\\d{5,8}",,,"22345678",,,[8]],[,,"6[6-9]\\d{7}","\\d{9}",,,"661234567",,,[9]],[,,"800\\d{4}","\\d{7}",,,"8001234",,,[7]],[,,"900\\d{3}","\\d{6}",,,"900123",,,[6]],[,,"808\\d{3}","\\d{6}",,,"808123",,,[6]],[,,"700\\d{5}","\\d{8}",,,"70012345",,,[8]],[,,"NA","NA",,,,,,[-1]],"AL",355,"00","0",,,"0",,,,[[,"(4)(\\d{3})(\\d{4})","$1 $2 $3",["4[0-6]"],"0$1"],[,"(6[6-9])(\\d{3})(\\d{4})","$1 $2 $3",["6"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["[2358][2-5]|4[7-9]"],"0$1"],[,"(\\d{3})(\\d{3,5})",
"$1 $2",["[235][16-9]|8[016-9]|[79]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],AM:[,[,,"[1-9]\\d{7}","\\d{5,8}",,,,,,[8],[5,6]],[,,"(?:1[0-2]\\d|2(?:2[2-46]|3[1-8]|4[2-69]|5[2-7]|6[1-9]|8[1-7])|3[12]2|47\\d)\\d{5}","\\d{5,8}",,,"10123456"],[,,"(?:4[139]|55|77|9[1-9])\\d{6}","\\d{8}",,,"77123456"],[,,"800\\d{5}","\\d{8}",,,"80012345"],[,,"90[016]\\d{5}","\\d{8}",,,"90012345"],[,,"80[1-4]\\d{5}","\\d{8}",,,"80112345"],[,,"NA","NA",
,,,,,[-1]],[,,"60(?:2[078]|[3-7]\\d|8[0-5])\\d{4}","\\d{8}",,,"60271234"],"AM",374,"00","0",,,"0",,,,[[,"(\\d{2})(\\d{6})","$1 $2",["1|47"],"(0$1)"],[,"(\\d{2})(\\d{6})","$1 $2",["4[139]|[5-7]|9[1-9]"],"0$1"],[,"(\\d{3})(\\d{5})","$1 $2",["[23]"],"(0$1)"],[,"(\\d{3})(\\d{2})(\\d{3})","$1 $2 $3",["8|90"],"0 $1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],AO:[,[,,"[29]\\d{8}","\\d{9}",,,,,,[9]],[,,"2\\d(?:[26-9]\\d|\\d[26-9])\\d{5}","\\d{9}",
,,"222123456"],[,,"9[1-49]\\d{7}","\\d{9}",,,"923123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"AO",244,"00",,,,,,,,[[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],AR:[,[,,"11\\d{8}|[2368]\\d{9}|9\\d{10}","\\d{6,11}",,,,,,[10,11],[6,7,8]],[,,"11\\d{8}|(?:2(?:2(?:[013]\\d|2[13-79]|4[1-6]|5[2457]|6[124-8]|7[1-4]|8[13-6]|9[1267])|3(?:1[467]|2[03-6]|3[13-8]|[49][2-6]|5[2-8]|[067]\\d)|4(?:7[3-8]|9\\d)|6(?:[01346]\\d|2[24-6]|5[15-8])|80\\d|9(?:[0124789]\\d|3[1-6]|5[234]|6[2-46]))|3(?:3(?:2[79]|6\\d|8[2578])|4(?:[78]\\d|0[0124-9]|[1-35]\\d|4[24-7]|6[02-9]|9[123678])|5(?:[138]\\d|2[1245]|4[1-9]|6[2-4]|7[1-6])|6[24]\\d|7(?:[0469]\\d|1[1568]|2[013-9]|3[145]|5[14-8]|7[2-57]|8[0-24-9])|8(?:[013578]\\d|2[15-7]|4[13-6]|6[1-357-9]|9[124]))|670\\d)\\d{6}",
"\\d{6,10}",,,"1123456789",,,[10]],[,,"675\\d{7}|9(?:11[2-9]\\d{7}|(?:2(?:2[013]|3[067]|49|6[01346]|80|9[147-9])|3(?:36|4[12358]|5[138]|6[24]|7[069]|8[013578]))[2-9]\\d{6}|\\d{4}[2-9]\\d{5})","\\d{6,11}",,,"91123456789",,,[11]],[,,"800\\d{7}","\\d{10}",,,"8001234567",,,[10]],[,,"60[04579]\\d{7}","\\d{10}",,,"6001234567",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"AR",54,"00","0",,,"0?(?:(11|2(?:2(?:02?|[13]|2[13-79]|4[1-6]|5[2457]|6[124-8]|7[1-4]|8[13-6]|9[1267])|3(?:02?|1[467]|2[03-6]|3[13-8]|[49][2-6]|5[2-8]|[67])|4(?:7[3-578]|9)|6(?:[0136]|2[24-6]|4[6-8]?|5[15-8])|80|9(?:0[1-3]|[19]|2\\d|3[1-6]|4[02568]?|5[2-4]|6[2-46]|72?|8[23]?))|3(?:3(?:2[79]|6|8[2578])|4(?:0[0-24-9]|[12]|3[5-8]?|4[24-7]|5[4-68]?|6[02-9]|7[126]|8[2379]?|9[1-36-8])|5(?:1|2[1245]|3[237]?|4[1-46-9]|6[2-4]|7[1-6]|8[2-5]?)|6[24]|7(?:[069]|1[1568]|2[15]|3[145]|4[13]|5[14-8]|7[2-57]|8[126])|8(?:[01]|2[15-7]|3[2578]?|4[13-6]|5[4-8]?|6[1-357-9]|7[36-8]?|8[5-8]?|9[124])))?15)?",
"9$1",,,[[,"([68]\\d{2})(\\d{3})(\\d{4})","$1-$2-$3",["[68]"],"0$1"],[,"(\\d{2})(\\d{4})","$1-$2",["[2-9]"],"$1"],[,"(\\d{3})(\\d{4})","$1-$2",["[2-9]"],"$1"],[,"(\\d{4})(\\d{4})","$1-$2",["[2-9]"],"$1"],[,"(9)(11)(\\d{4})(\\d{4})","$2 15-$3-$4",["911"],"0$1"],[,"(9)(\\d{3})(\\d{3})(\\d{4})","$2 15-$3-$4",["9(?:2[234689]|3[3-8])","9(?:2(?:2[013]|3[067]|49|6[01346]|80|9[147-9])|3(?:36|4[1-358]|5[138]|6[24]|7[069]|8[013578]))","9(?:2(?:2(?:0[013-9]|[13])|3(?:0[013-9]|[67])|49|6(?:[0136]|4[0-59])|8|9(?:[19]|44|7[013-9]|8[14]))|3(?:36|4(?:[12]|3[456]|[58]4)|5(?:1|3[0-24-689]|8[46])|6|7[069]|8(?:[01]|34|[578][45])))",
"9(?:2(?:2(?:0[013-9]|[13])|3(?:0[013-9]|[67])|49|6(?:[0136]|4[0-59])|8|9(?:[19]|44|7[013-9]|8[14]))|3(?:36|4(?:[12]|3(?:4|5[014]|6[1239])|[58]4)|5(?:1|3[0-24-689]|8[46])|6|7[069]|8(?:[01]|34|[578][45])))"],"0$1"],[,"(9)(\\d{4})(\\d{2})(\\d{4})","$2 15-$3-$4",["9[23]"],"0$1"],[,"(11)(\\d{4})(\\d{4})","$1 $2-$3",["1"],"0$1",,1],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2-$3",["2(?:2[013]|3[067]|49|6[01346]|80|9[147-9])|3(?:36|4[1-358]|5[138]|6[24]|7[069]|8[013578])","2(?:2(?:0[013-9]|[13])|3(?:0[013-9]|[67])|49|6(?:[0136]|4[0-59])|8|9(?:[19]|44|7[013-9]|8[14]))|3(?:36|4(?:[12]|3[456]|[58]4)|5(?:1|3[0-24-689]|8[46])|6|7[069]|8(?:[01]|34|[578][45]))",
"2(?:2(?:0[013-9]|[13])|3(?:0[013-9]|[67])|49|6(?:[0136]|4[0-59])|8|9(?:[19]|44|7[013-9]|8[14]))|3(?:36|4(?:[12]|3(?:4|5[014]|6[1239])|[58]4)|5(?:1|3[0-24-689]|8[46])|6|7[069]|8(?:[01]|34|[578][45]))"],"0$1",,1],[,"(\\d{4})(\\d{2})(\\d{4})","$1 $2-$3",["[23]"],"0$1",,1],[,"(\\d{3})","$1",["1[012]|911"],"$1"]],[[,"([68]\\d{2})(\\d{3})(\\d{4})","$1-$2-$3",["[68]"],"0$1"],[,"(9)(11)(\\d{4})(\\d{4})","$1 $2 $3-$4",["911"]],[,"(9)(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3-$4",["9(?:2[234689]|3[3-8])","9(?:2(?:2[013]|3[067]|49|6[01346]|80|9[147-9])|3(?:36|4[1-358]|5[138]|6[24]|7[069]|8[013578]))",
"9(?:2(?:2(?:0[013-9]|[13])|3(?:0[013-9]|[67])|49|6(?:[0136]|4[0-59])|8|9(?:[19]|44|7[013-9]|8[14]))|3(?:36|4(?:[12]|3[456]|[58]4)|5(?:1|3[0-24-689]|8[46])|6|7[069]|8(?:[01]|34|[578][45])))","9(?:2(?:2(?:0[013-9]|[13])|3(?:0[013-9]|[67])|49|6(?:[0136]|4[0-59])|8|9(?:[19]|44|7[013-9]|8[14]))|3(?:36|4(?:[12]|3(?:4|5[014]|6[1239])|[58]4)|5(?:1|3[0-24-689]|8[46])|6|7[069]|8(?:[01]|34|[578][45])))"]],[,"(9)(\\d{4})(\\d{2})(\\d{4})","$1 $2 $3-$4",["9[23]"]],[,"(11)(\\d{4})(\\d{4})","$1 $2-$3",["1"],"0$1",
,1],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2-$3",["2(?:2[013]|3[067]|49|6[01346]|80|9[147-9])|3(?:36|4[1-358]|5[138]|6[24]|7[069]|8[013578])","2(?:2(?:0[013-9]|[13])|3(?:0[013-9]|[67])|49|6(?:[0136]|4[0-59])|8|9(?:[19]|44|7[013-9]|8[14]))|3(?:36|4(?:[12]|3[456]|[58]4)|5(?:1|3[0-24-689]|8[46])|6|7[069]|8(?:[01]|34|[578][45]))","2(?:2(?:0[013-9]|[13])|3(?:0[013-9]|[67])|49|6(?:[0136]|4[0-59])|8|9(?:[19]|44|7[013-9]|8[14]))|3(?:36|4(?:[12]|3(?:4|5[014]|6[1239])|[58]4)|5(?:1|3[0-24-689]|8[46])|6|7[069]|8(?:[01]|34|[578][45]))"],
"0$1",,1],[,"(\\d{4})(\\d{2})(\\d{4})","$1 $2-$3",["[23]"],"0$1",,1]],[,,"NA","NA",,,,,,[-1]],,,[,,"810\\d{7}","\\d{10}",,,"8101234567",,,[10]],[,,"810\\d{7}","\\d{10}",,,"8101234567",,,[10]],,,[,,"NA","NA",,,,,,[-1]]],AS:[,[,,"[5689]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"6846(?:22|33|44|55|77|88|9[19])\\d{4}","\\d{7}(?:\\d{3})?",,,"6846221234"],[,,"684(?:2(?:5[2468]|72)|7(?:3[13]|70))\\d{4}","\\d{7}(?:\\d{3})?",,,"6847331234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],
[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"AS",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"684",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],AT:[,[,,"[1-9]\\d{3,12}","\\d{3,13}",,,,,,[4,5,6,7,8,9,10,11,12,13],[3]],[,,"1\\d{3,12}|(?:2(?:1[467]|2[13-8]|5[2357]|6[1-46-8]|7[1-8]|8[124-7]|9[1458])|3(?:1[1-8]|3[23568]|4[5-7]|5[1378]|6[1-38]|8[3-68])|4(?:2[1-8]|35|63|7[1368]|8[2457])|5(?:12|2[1-8]|3[357]|4[147]|5[12578]|6[37])|6(?:13|2[1-47]|4[1-35-8]|5[468]|62)|7(?:2[1-8]|3[25]|4[13478]|5[68]|6[16-8]|7[1-6]|9[45]))\\d{3,10}",
"\\d{3,13}",,,"1234567890"],[,,"6(?:5[0-3579]|6[013-9]|[7-9]\\d)\\d{4,10}","\\d{7,13}",,,"664123456",,,[7,8,9,10,11,12,13]],[,,"800\\d{6,10}","\\d{9,13}",,,"800123456",,,[9,10,11,12,13]],[,,"9(?:0[01]|3[019])\\d{6,10}","\\d{9,13}",,,"900123456",,,[9,10,11,12,13]],[,,"8(?:10\\d|2(?:[01]\\d|8\\d?))\\d{5,9}","\\d{8,13}",,,"810123456",,,[8,9,10,11,12,13]],[,,"NA","NA",,,,,,[-1]],[,,"780\\d{6,10}","\\d{9,13}",,,"780123456",,,[9,10,11,12,13]],"AT",43,"00","0",,,"0",,,,[[,"(116\\d{3})","$1",["116"],"$1"],
[,"(1)(\\d{3,12})","$1 $2",["1"],"0$1"],[,"(5\\d)(\\d{3,5})","$1 $2",["5[079]"],"0$1"],[,"(5\\d)(\\d{3})(\\d{3,4})","$1 $2 $3",["5[079]"],"0$1"],[,"(5\\d)(\\d{4})(\\d{4,7})","$1 $2 $3",["5[079]"],"0$1"],[,"(\\d{3})(\\d{3,10})","$1 $2",["316|46|51|732|6(?:5[0-3579]|[6-9])|7(?:[28]0)|[89]"],"0$1"],[,"(\\d{4})(\\d{3,9})","$1 $2",["2|3(?:1[1-578]|[3-8])|4[2378]|5[2-6]|6(?:[12]|4[1-9]|5[468])|7(?:2[1-8]|35|4[1-8]|[5-79])"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"5(?:(?:0[1-9]|17)\\d{2,10}|[79]\\d{3,11})|720\\d{6,10}",
"\\d{5,13}",,,"50123",,,[5,6,7,8,9,10,11,12,13]],,,[,,"NA","NA",,,,,,[-1]]],AU:[,[,,"[1-578]\\d{5,9}","\\d{6,10}",,,,,,[5,6,7,8,9,10]],[,,"[237]\\d{8}|8(?:[6-8]\\d{3}|9(?:[02-9]\\d{2}|1(?:[0-57-9]\\d|6[0135-9])))\\d{4}","\\d{8,9}",,,"212345678",,,[9]],[,,"14(?:5\\d|71)\\d{5}|4(?:[0-3]\\d|4[47-9]|5[0-25-9]|6[6-9]|7[02-9]|8[147-9]|9[017-9])\\d{6}","\\d{9}",,,"412345678",,,[9]],[,,"180(?:0\\d{3}|2)\\d{3}","\\d{7,10}",,,"1800123456",,,[7,10]],[,,"19(?:0[0126]\\d|[679])\\d{5}","\\d{8,10}",,,"1900123456",
,,[8,10]],[,,"13(?:00\\d{3}|45[0-4]|\\d)\\d{3}","\\d{6,10}",,,"1300123456",,,[6,8,10]],[,,"500\\d{6}","\\d{9}",,,"500123456",,,[9]],[,,"550\\d{6}","\\d{9}",,,"550123456",,,[9]],"AU",61,"(?:14(?:1[14]|34|4[17]|[56]6|7[47]|88))?001[14-689]","0",,,"0",,"0011",,[[,"([2378])(\\d{4})(\\d{4})","$1 $2 $3",["[2378]"],"(0$1)"],[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3",["[45]|14"],"0$1"],[,"(16)(\\d{3})(\\d{2,4})","$1 $2 $3",["16"],"0$1"],[,"(1[389]\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["1(?:[38]0|90)","1(?:[38]00|90)"],
"$1"],[,"(180)(2\\d{3})","$1 $2",["180","1802"],"$1"],[,"(19\\d)(\\d{3})","$1 $2",["19[13]"],"$1"],[,"(19\\d{2})(\\d{4})","$1 $2",["19[679]"],"$1"],[,"(13)(\\d{2})(\\d{2})","$1 $2 $3",["13[1-9]"],"$1"]],,[,,"16\\d{3,7}","\\d{5,9}",,,"1612345",,,[5,6,7,8,9]],1,,[,,"1(?:3(?:00\\d{3}|45[0-4]|\\d)\\d{3}|80(?:0\\d{6}|2\\d{3}))","\\d{6,10}",,,"1300123456",,,[6,7,8,10]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],AW:[,[,,"[25-9]\\d{6}","\\d{7}",,,,,,[7]],[,,"5(?:2\\d|8[1-9])\\d{4}","\\d{7}",,,"5212345"],
[,,"(?:5(?:6\\d|9[2-478])|6(?:[039]0|22|4[01]|6[0-2])|7[34]\\d|9(?:6[45]|9[4-8]))\\d{4}","\\d{7}",,,"5601234"],[,,"800\\d{4}","\\d{7}",,,"8001234"],[,,"900\\d{4}","\\d{7}",,,"9001234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"28\\d{5}|501\\d{4}","\\d{7}",,,"5011234"],"AW",297,"00",,,,,,,,[[,"(\\d{3})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],AX:[,[,,"[135]\\d{5,9}|[27]\\d{4,9}|4\\d{5,10}|6\\d{7,8}|8\\d{6,9}",
"\\d{5,12}",,,,,,[5,6,7,8,9,10,11,12]],[,,"18[1-8]\\d{3,9}","\\d{6,12}",,,"1812345678",,,[6,7,8,9,10,11,12]],[,,"4\\d{5,10}|50\\d{4,8}","\\d{6,11}",,,"412345678",,,[6,7,8,9,10,11]],[,,"800\\d{4,7}","\\d{7,10}",,,"8001234567",,,[7,8,9,10]],[,,"[67]00\\d{5,6}","\\d{8,9}",,,"600123456",,,[8,9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"AX",358,"00|99(?:[02469]|5(?:11|33|5[59]|88|9[09]))","0",,,"0",,"00",,,,[,,"NA","NA",,,,,,[-1]],,,[,,"[13]00\\d{3,7}|2(?:0(?:0\\d{3,7}|2[023]\\d{1,6}|9[89]\\d{1,6}))|60(?:[12]\\d{5,6}|6\\d{7})|7(?:1\\d{7}|3\\d{8}|5[03-9]\\d{2,7})",
"\\d{5,10}",,,"100123",,,[5,6,7,8,9,10]],[,,"[13]0\\d{4,8}|2(?:0(?:[016-8]\\d{3,7}|[2-59]\\d{2,7})|9\\d{4,8})|60(?:[12]\\d{5,6}|6\\d{7})|7(?:1\\d{7}|3\\d{8}|5[03-9]\\d{2,7})","\\d{5,10}",,,"10112345",,,[5,6,7,8,9,10]],,,[,,"NA","NA",,,,,,[-1]]],AZ:[,[,,"[1-9]\\d{8}","\\d{7,9}",,,,,,[9],[7]],[,,"(?:1[28]\\d{3}|2(?:02|1[24]|2[2-4]|33|[45]2|6[23])\\d{2}|365(?:[0-46-9]\\d|5[0-35-9]))\\d{4}","\\d{7,9}",,,"123123456"],[,,"(?:36554|(?:4[04]|5[015]|60|7[07])\\d{3})\\d{4}","\\d{9}",,,"401234567"],[,,"88\\d{7}",
"\\d{9}",,,"881234567"],[,,"900200\\d{3}","\\d{9}",,,"900200123"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"AZ",994,"00","0",,,"0",,,,[[,"(\\d{2})(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3 $4",["(?:1[28]|2(?:[45]2|[0-36])|365)"],"(0$1)"],[,"(\\d{2})(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[4-8]"],"0$1"],[,"(\\d{3})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["9"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BA:[,
[,,"[3-9]\\d{7,8}","\\d{6,9}",,,,,,[8,9],[6]],[,,"(?:[35]\\d|49)\\d{6}","\\d{6,8}",,,"30123456",,,[8]],[,,"6(?:03|44|71|[1-356])\\d{6}","\\d{8,9}",,,"61123456"],[,,"8[08]\\d{6}","\\d{8}",,,"80123456",,,[8]],[,,"9[0246]\\d{6}","\\d{8}",,,"90123456",,,[8]],[,,"8[12]\\d{6}","\\d{8}",,,"82123456",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"BA",387,"00","0",,,"0",,,,[[,"(\\d{2})(\\d{3})(\\d{3})","$1 $2-$3",["[3-5]"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["6[1-356]|[7-9]"],"0$1"],[,
"(\\d{2})(\\d{2})(\\d{2})(\\d{3})","$1 $2 $3 $4",["6[047]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"70[23]\\d{5}","\\d{8}",,,"70223456",,,[8]],,,[,,"NA","NA",,,,,,[-1]]],BB:[,[,,"[2589]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"246(?:2(?:2[78]|7[0-4])|4(?:1[024-6]|2\\d|3[2-9])|5(?:20|[34]\\d|54|7[1-3])|6(?:2\\d|38)|7(?:37|57)|9(?:1[89]|63))\\d{4}","\\d{7}(?:\\d{3})?",,,"2464123456"],[,,"246(?:2(?:[356]\\d|4[0-57-9]|8[0-79])|45\\d|8(?:[2-5]\\d|83))\\d{4}","\\d{7}(?:\\d{3})?",
,,"2462501234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],[,,"900\\d{7}|246976\\d{4}","\\d{7}(?:\\d{3})?",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"24631\\d{5}","\\d{7}(?:\\d{3})?",,,"2463101234"],"BB",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"246",[,,"NA","NA",,,,,,[-1]],[,,"246(?:292|41[7-9]|43[01])\\d{4}","\\d{7}(?:\\d{3})?",,,"2464301234"],,,[,,"NA","NA",,,,,,[-1]]],BD:[,[,,"[2-79]\\d{5,9}|1\\d{9}|8[0-7]\\d{4,8}",
"\\d{6,10}",,,,,,[6,7,8,9,10]],[,,"2(?:550\\d|7(?:1[0-267]|2[0-289]|3[0-29]|4[01]|5[1-3]|6[013]|7[017]|91)|8(?:0[125]|[139][1-6]|2[0157-9]|6[1-35]|7[1-5]|8[1-8]|90)|9(?:0[0-2]|1[0-4]|2[568]|3[3-6]|5[5-7]|6[0167]|7[15]|8[0146-8]))\\d{4}|3(?:12?[5-7]\\d{2}|0(?:2(?:[025-79]\\d|[348]\\d{1,2})|3(?:[2-4]\\d|[56]\\d?))|2(?:1\\d{2}|2(?:[12]\\d|[35]\\d{1,2}|4\\d?))|3(?:1\\d{2}|2(?:[2356]\\d|4\\d{1,2}))|4(?:1\\d{2}|2(?:2\\d{1,2}|[47]|5\\d{2}))|5(?:1\\d{2}|29)|[67]1\\d{2}|8(?:1\\d{2}|2(?:2\\d{2}|3|4\\d)))\\d{3}|4(?:0(?:2(?:[09]\\d|7)|33\\d{2})|1\\d{3}|2(?:1\\d{2}|2(?:[25]\\d?|[348]\\d|[67]\\d{1,2}))|3(?:1\\d{2}(?:\\d{2})?|2(?:[045]\\d|[236-9]\\d{1,2})|32\\d{2})|4(?:[18]\\d{2}|2(?:[2-46]\\d{2}|3)|5[25]\\d{2})|5(?:1\\d{2}|2(?:3\\d|5))|6(?:[18]\\d{2}|2(?:3(?:\\d{2})?|[46]\\d{1,2}|5\\d{2}|7\\d)|5(?:3\\d?|4\\d|[57]\\d{1,2}|6\\d{2}|8))|71\\d{2}|8(?:[18]\\d{2}|23\\d{2}|54\\d{2})|9(?:[18]\\d{2}|2[2-5]\\d{2}|53\\d{1,2}))\\d{3}|5(?:02[03489]\\d{2}|1\\d{2}|2(?:1\\d{2}|2(?:2(?:\\d{2})?|[457]\\d{2}))|3(?:1\\d{2}|2(?:[37](?:\\d{2})?|[569]\\d{2}))|4(?:1\\d{2}|2[46]\\d{2})|5(?:1\\d{2}|26\\d{1,2})|6(?:[18]\\d{2}|2|53\\d{2})|7(?:1|24)\\d{2}|8(?:1|26)\\d{2}|91\\d{2})\\d{3}|6(?:0(?:1\\d{2}|2(?:3\\d{2}|4\\d{1,2}))|2(?:2[2-5]\\d{2}|5(?:[3-5]\\d{2}|7)|8\\d{2})|3(?:1|2[3478])\\d{2}|4(?:1|2[34])\\d{2}|5(?:1|2[47])\\d{2}|6(?:[18]\\d{2}|6(?:2(?:2\\d|[34]\\d{2})|5(?:[24]\\d{2}|3\\d|5\\d{1,2})))|72[2-5]\\d{2}|8(?:1\\d{2}|2[2-5]\\d{2})|9(?:1\\d{2}|2[2-6]\\d{2}))\\d{3}|7(?:(?:02|[3-589]1|6[12]|72[24])\\d{2}|21\\d{3}|32)\\d{3}|8(?:(?:4[12]|[5-7]2|1\\d?)|(?:0|3[12]|[5-7]1|217)\\d)\\d{4}|9(?:[35]1|(?:[024]2|81)\\d|(?:1|[24]1)\\d{2})\\d{3}",
"\\d{6,9}",,,"27111234",,,[6,7,8,9]],[,,"(?:1[13-9]\\d|(?:3[78]|44)[02-9]|6(?:44|6[02-9]))\\d{7}","\\d{10}",,,"1812345678",,,[10]],[,,"80[03]\\d{7}","\\d{10}",,,"8001234567",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"96(?:0[49]|1[0-4]|6[69])\\d{6}","\\d{10}",,,"9604123456",,,[10]],"BD",880,"00","0",,,"0",,,,[[,"(2)(\\d{7,8})","$1-$2",["2"],"0$1"],[,"(\\d{2})(\\d{4,6})","$1-$2",["[3-79]1"],"0$1"],[,"(\\d{4})(\\d{3,6})","$1-$2",["1|3(?:0|[2-58]2)|4(?:0|[25]2|3[23]|[4689][25])|5(?:[02-578]2|6[25])|6(?:[0347-9]2|[26][25])|7[02-9]2|8(?:[023][23]|[4-7]2)|9(?:[02][23]|[458]2|6[016])"],
"0$1"],[,"(\\d{3})(\\d{3,7})","$1-$2",["[3-79][2-9]|8"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BE:[,[,,"[1-9]\\d{7,8}","\\d{8,9}",,,,,,[8,9]],[,,"(?:1[0-69]|[23][2-8]|4[23]|5\\d|6[013-57-9]|71|8[1-79]|9[2-4])\\d{6}|80[2-8]\\d{5}","\\d{8}",,,"12345678",,,[8]],[,,"4(?:6[0135-8]|[79]\\d|8[3-9])\\d{6}","\\d{9}",,,"470123456",,,[9]],[,,"800\\d{5}","\\d{8}",,,"80012345",,,[8]],[,,"(?:70[2-467]|90[0-79])\\d{5}","\\d{8}",,,"90123456",
,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"BE",32,"00","0",,,"0",,,,[[,"(\\d{3})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["4[6-9]"],"0$1"],[,"(\\d)(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[23]|4[23]|9[2-4]"],"0$1"],[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[156]|7[018]|8(?:0[1-9]|[1-79])"],"0$1"],[,"(\\d{3})(\\d{2})(\\d{3})","$1 $2 $3",["(?:80|9)0"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"78\\d{6}","\\d{8}",,,"78123456",,,[8]],
,,[,,"NA","NA",,,,,,[-1]]],BF:[,[,,"[25-7]\\d{7}","\\d{8}",,,,,,[8]],[,,"2(?:0(?:49|5[23]|9[016-9])|4(?:4[569]|5[4-6]|7[0179])|5(?:[34]\\d|50))\\d{4}","\\d{8}",,,"20491234"],[,,"(?:55[0-5]|6(?:[0-689]\\d|7[0-5]))\\d{5}|7\\d{7}","\\d{8}",,,"70123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"BF",226,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA",
"NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BG:[,[,,"[23567]\\d{5,7}|[489]\\d{6,8}","\\d{5,9}",,,,,,[6,7,8,9],[4,5]],[,,"2\\d{5,7}|(?:[36]\\d|5[1-9]|8[1-6]|9[1-7])\\d{5,6}|(?:4(?:[124-7]\\d|3[1-6])|7(?:0[1-9]|[1-9]\\d))\\d{4,5}","\\d{5,8}",,,"2123456",,,[6,7,8]],[,,"(?:8[7-9]\\d|9(?:8\\d|99))\\d{6}|4(?:3[0789]|8\\d)\\d{5}","\\d{8,9}",,,"48123456",,,[8,9]],[,,"800\\d{5}","\\d{8}",,,"80012345",,,[8]],[,,"90\\d{6}","\\d{8}",,,"90123456",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"700\\d{5}","\\d{5,9}",,,"70012345",
,,[8]],[,,"NA","NA",,,,,,[-1]],"BG",359,"00","0",,,"0",,,,[[,"(2)(\\d)(\\d{2})(\\d{2})","$1 $2 $3 $4",["2"],"0$1"],[,"(2)(\\d{3})(\\d{3,4})","$1 $2 $3",["2"],"0$1"],[,"(\\d{3})(\\d{4})","$1 $2",["43[124-7]|70[1-9]"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{2})","$1 $2 $3",["43[124-7]|70[1-9]"],"0$1"],[,"(\\d{3})(\\d{2})(\\d{3})","$1 $2 $3",["[78]00"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3",["999"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{2,3})","$1 $2 $3",["[356]|4[124-7]|7[1-9]|8[1-6]|9[1-7]"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{3,4})",
"$1 $2 $3",["48|8[7-9]|9[08]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BH:[,[,,"[136-9]\\d{7}","\\d{8}",,,,,,[8]],[,,"(?:1(?:3[1356]|6[0156]|7\\d)\\d|6(?:1[16]\\d|500|6(?:0\\d|3[12]|44|7[7-9])|9[69][69])|7(?:1(?:11|78)|7\\d{2}))\\d{4}","\\d{8}",,,"17001234"],[,,"(?:3(?:[1-4679]\\d|5[013-69]|8[0-47-9])\\d|6(?:3(?:00|33|6[16])|6(?:[69]\\d|3[03-9]|7[0-6])))\\d{4}","\\d{8}",,,"36001234"],[,,"80\\d{6}","\\d{8}",,,"80123456"],[,,"(?:87|9[014578])\\d{6}",
"\\d{8}",,,"90123456"],[,,"84\\d{6}","\\d{8}",,,"84123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"BH",973,"00",,,,,,,,[[,"(\\d{4})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BI:[,[,,"[267]\\d{7}","\\d{8}",,,,,,[8]],[,,"22\\d{6}","\\d{8}",,,"22201234"],[,,"(?:29|6[189]|7[124-9])\\d{6}","\\d{8}",,,"79561234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA",
"NA",,,,,,[-1]],"BI",257,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BJ:[,[,,"[2689]\\d{7}|7\\d{3}","\\d{4,8}",,,,,,[4,8]],[,,"2(?:02|1[037]|2[45]|3[68])\\d{5}","\\d{8}",,,"20211234",,,[8]],[,,"(?:6[1-8]|9[03-9])\\d{6}","\\d{8}",,,"90011234",,,[8]],[,,"7[3-5]\\d{2}","\\d{4}",,,"7312",,,[4]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"857[58]\\d{4}",
"\\d{8}",,,"85751234",,,[8]],"BJ",229,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"81\\d{6}","\\d{8}",,,"81123456",,,[8]],,,[,,"NA","NA",,,,,,[-1]]],BL:[,[,,"[56]\\d{8}","\\d{9}",,,,,,[9]],[,,"590(?:2[7-9]|5[12]|87)\\d{4}","\\d{9}",,,"590271234"],[,,"690(?:0[0-7]|[1-9]\\d)\\d{4}","\\d{9}",,,"690301234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],
"BL",590,"00","0",,,"0",,,,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BM:[,[,,"[4589]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"441(?:2(?:02|23|61|[3479]\\d)|[46]\\d{2}|5(?:4\\d|60|89)|824)\\d{4}","\\d{7}(?:\\d{3})?",,,"4412345678"],[,,"441(?:[37]\\d|5[0-39])\\d{5}","\\d{7}(?:\\d{3})?",,,"4413701234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],
[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"BM",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"441",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BN:[,[,,"[2-578]\\d{6}","\\d{7}",,,,,,[7]],[,,"2(?:[013-9]\\d|2[0-7])\\d{4}|[3-5]\\d{6}","\\d{7}",,,"2345678"],[,,"22[89]\\d{4}|[78]\\d{6}","\\d{7}",,,"7123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,
,[-1]],"BN",673,"00",,,,,,,,[[,"([2-578]\\d{2})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BO:[,[,,"[23467]\\d{7}","\\d{7,8}",,,,,,[8],[7]],[,,"(?:2(?:2\\d{2}|5(?:11|[258]\\d|9[67])|6(?:12|2\\d|9[34])|8(?:2[34]|39|62))|3(?:3\\d{2}|4(?:6\\d|8[24])|8(?:25|42|5[257]|86|9[25])|9(?:2\\d|3[234]|4[248]|5[24]|6[2-6]|7\\d))|4(?:4\\d{2}|6(?:11|[24689]\\d|72)))\\d{4}","\\d{7,8}",,,"22123456"],[,,"[67]\\d{7}","\\d{8}",,,"71234567"],
[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"BO",591,"00(1\\d)?","0",,,"0(1\\d)?",,,,[[,"([234])(\\d{7})","$1 $2",["[234]"],,"0$CC $1"],[,"([67]\\d{7})","$1",["[67]"],,"0$CC $1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BQ:[,[,,"[347]\\d{6}","\\d{7}",,,,,,[7]],[,,"(?:318[023]|416[023]|7(?:1[578]|50)\\d)\\d{3}","\\d{7}",,,"7151234"],[,,"(?:318[14-68]|416[15-9]|7(?:0[01]|7[07]|[89]\\d)\\d)\\d{3}",
"\\d{7}",,,"3181234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"BQ",599,"00",,,,,,,,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BR:[,[,,"[1-46-9]\\d{7,10}|5(?:[0-4]\\d{7,9}|5(?:[2-8]\\d{7}|9\\d{7,8}))","\\d{8,11}",,,,,,[8,9,10,11]],[,,"(?:[14689][1-9]|2[12478]|3[1-578]|5[1-5]|7[13-579])[2-5]\\d{7}","\\d{8,11}",,,"1123456789",,,[10]],[,,"1[1-9](?:7|9\\d)\\d{7}|(?:2[12478]|3[1-578]|[4689][1-9]|5[1-5]|7[13-579])(?:[6-8]|9\\d?)\\d{7}",
"\\d{8,11}",,,"11961234567",,,[10,11]],[,,"800\\d{6,7}","\\d{8,11}",,,"800123456",,,[9,10]],[,,"(?:300|[59]00\\d?)\\d{6}","\\d{8,11}",,,"300123456",,,[9,10]],[,,"(?:300\\d(?:\\d{2})?|40(?:0\\d|20))\\d{4}","\\d{8,10}",,,"40041234",,,[8,10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"BR",55,"00(?:1[245]|2[1-35]|31|4[13]|[56]5|99)","0",,,"0(?:(1[245]|2[1-35]|31|4[13]|[56]5|99)(\\d{10,11}))?","$2",,,[[,"(\\d{4})(\\d{4})","$1-$2",["[2-9](?:[1-9]|0[1-9])"],"$1"],[,"(\\d{5})(\\d{4})","$1-$2",["9(?:[1-9]|0[1-9])"],
"$1"],[,"(\\d{3,5})","$1",["1[125689]"],"$1"],[,"(\\d{2})(\\d{5})(\\d{4})","$1 $2-$3",["(?:[14689][1-9]|2[12478]|3[1-578]|5[1-5]|7[13-579])9"],"($1)","0 $CC ($1)"],[,"(\\d{2})(\\d{4})(\\d{4})","$1 $2-$3",["[1-9][1-9]"],"($1)","0 $CC ($1)"],[,"(\\d{4})(\\d{4})","$1-$2",["(?:300|40(?:0|20))"]],[,"([3589]00)(\\d{2,3})(\\d{4})","$1 $2 $3",["[3589]00"],"0$1"]],[[,"(\\d{2})(\\d{5})(\\d{4})","$1 $2-$3",["(?:[14689][1-9]|2[12478]|3[1-578]|5[1-5]|7[13-579])9"],"($1)","0 $CC ($1)"],[,"(\\d{2})(\\d{4})(\\d{4})",
"$1 $2-$3",["[1-9][1-9]"],"($1)","0 $CC ($1)"],[,"(\\d{4})(\\d{4})","$1-$2",["(?:300|40(?:0|20))"]],[,"([3589]00)(\\d{2,3})(\\d{4})","$1 $2 $3",["[3589]00"],"0$1"]],[,,"NA","NA",,,,,,[-1]],,,[,,"(?:300\\d|40(?:0\\d|20))\\d{4}","\\d{8}",,,"40041234",,,[8]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BS:[,[,,"[2589]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"242(?:3(?:02|[236][1-9]|4[0-24-9]|5[0-68]|7[3467]|8[0-4]|9[2-467])|461|502|6(?:0[1-3]|12|7[67]|8[78]|9[89])|7(?:02|88))\\d{4}","\\d{7}(?:\\d{3})?",
,,"2423456789"],[,,"242(?:3(?:5[79]|[79]5)|4(?:[2-4][1-9]|5[1-8]|6[2-8]|7\\d|81)|5(?:2[45]|3[35]|44|5[1-9]|65|77)|6[34]6|727)\\d{4}","\\d{7}(?:\\d{3})?",,,"2423591234"],[,,"242300\\d{4}|8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{7}(?:\\d{3})?",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"BS",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"242",[,,"NA","NA",,,,,,[-1]],
[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BT:[,[,,"[1-8]\\d{6,7}","\\d{6,8}",,,,,,[7,8],[6]],[,,"(?:2[3-6]|[34][5-7]|5[236]|6[2-46]|7[246]|8[2-4])\\d{5}","\\d{6,7}",,,"2345678",,,[7]],[,,"(?:1[67]|77)\\d{6}","\\d{8}",,,"17123456",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"BT",975,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["1|77"]],[,"([2-8])(\\d{3})(\\d{3})","$1 $2 $3",["[2-68]|7[246]"]]],
,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BW:[,[,,"[2-79]\\d{6,7}","\\d{7,8}",,,,,,[7,8]],[,,"(?:2(?:4[0-48]|6[0-24]|9[0578])|3(?:1[0235-9]|55|[69]\\d|7[01])|4(?:6[03]|7[1267]|9[0-5])|5(?:3[0389]|4[0489]|7[1-47]|88|9[0-49])|6(?:2[1-35]|5[149]|8[067]))\\d{4}","\\d{7}",,,"2401234",,,[7]],[,,"7(?:[1-6]\\d|7[014-8])\\d{5}","\\d{8}",,,"71123456",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"90\\d{5}","\\d{7}",,,"9012345",,,[7]],[,,"NA","NA",,,,,,[-1]],[,
,"NA","NA",,,,,,[-1]],[,,"79[12][01]\\d{4}","\\d{8}",,,"79101234",,,[8]],"BW",267,"00",,,,,,,,[[,"(\\d{3})(\\d{4})","$1 $2",["[2-6]"]],[,"(7\\d)(\\d{3})(\\d{3})","$1 $2 $3",["7"]],[,"(90)(\\d{5})","$1 $2",["9"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BY:[,[,,"[1-4]\\d{8}|800\\d{3,7}|[89]\\d{9,10}","\\d{5,11}",,,,,,[6,7,8,9,10,11],[5]],[,,"(?:1(?:5(?:1[1-5]|[24]\\d|6[2-4]|9[1-7])|6(?:[235]\\d|4[1-7])|7\\d{2})|2(?:1(?:[246]\\d|3[0-35-9]|5[1-9])|2(?:[235]\\d|4[0-8])|3(?:[26]\\d|3[02-79]|4[024-7]|5[03-7])))\\d{5}",
"\\d{5,9}",,,"152450911",,,[9]],[,,"(?:2(?:5[5679]|9[1-9])|33\\d|44\\d)\\d{6}","\\d{9}",,,"294911911",,,[9]],[,,"8(?:0[13]|20\\d)\\d{7}|800\\d{3,7}","\\d{5,11}",,,"8011234567"],[,,"(?:810|902)\\d{7}","\\d{10}",,,"9021234567",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"249\\d{6}","\\d{9}",,,"249123456",,,[9]],"BY",375,"810","8",,,"8?0?",,"8~10",,[[,"(\\d{2})(\\d{3})(\\d{2})(\\d{2})","$1 $2-$3-$4",["17[0-3589]|2[4-9]|[34]","17(?:[02358]|1[0-2]|9[0189])|2[4-9]|[34]"],"8 0$1"],[,"(\\d{3})(\\d{2})(\\d{2})(\\d{2})",
"$1 $2-$3-$4",["1(?:5[24]|6[235]|7[467])|2(?:1[246]|2[25]|3[26])","1(?:5[24]|6(?:2|3[04-9]|5[0346-9])|7(?:[46]|7[37-9]))|2(?:1[246]|2[25]|3[26])"],"8 0$1"],[,"(\\d{4})(\\d{2})(\\d{3})","$1 $2-$3",["1(?:5[169]|6[3-5]|7[179])|2(?:1[35]|2[34]|3[3-5])","1(?:5[169]|6(?:3[1-3]|4|5[125])|7(?:1[3-9]|7[0-24-6]|9[2-7]))|2(?:1[35]|2[34]|3[3-5])"],"8 0$1"],[,"([89]\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["8[01]|9"],"8 $1"],[,"(82\\d)(\\d{4})(\\d{4})","$1 $2 $3",["82"],"8 $1"],[,"(800)(\\d{3})","$1 $2",["800"],"8 $1"],
[,"(800)(\\d{2})(\\d{2,4})","$1 $2 $3",["800"],"8 $1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"8(?:[013]|[12]0)\\d{8}|800\\d{3,7}|902\\d{7}","\\d{5,11}",,,"82012345678"],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],BZ:[,[,,"[2-8]\\d{6}|0\\d{10}","\\d{7}(?:\\d{4})?",,,,,,[7,11]],[,,"(?:[23458][02]\\d|7(?:[02]\\d|32))\\d{4}","\\d{7}",,,"2221234",,,[7]],[,,"6[0-35-7]\\d{5}","\\d{7}",,,"6221234",,,[7]],[,,"0800\\d{7}","\\d{11}",,,"08001234123",,,[11]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,
"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"BZ",501,"00",,,,,,,,[[,"(\\d{3})(\\d{4})","$1-$2",["[2-8]"]],[,"(0)(800)(\\d{4})(\\d{3})","$1-$2-$3-$4",["0"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],CA:[,[,,"[2-9]\\d{9}|3\\d{6}","\\d{7}(?:\\d{3})?",,,,,,[7,10]],[,,"(?:2(?:04|[23]6|[48]9|50)|3(?:06|43|65)|4(?:03|1[68]|3[178]|50)|5(?:06|1[49]|48|79|8[17])|6(?:0[04]|13|22|39|47)|7(?:0[59]|78|8[02])|8(?:[06]7|19|25|73)|90[25])[2-9]\\d{6}|310\\d{4}",
"\\d{7}(?:\\d{3})?",,,"2042345678",,,[10]],[,,"(?:2(?:04|[23]6|[48]9|50)|3(?:06|43|65)|4(?:03|1[68]|3[178]|50)|5(?:06|1[49]|48|79|8[17])|6(?:0[04]|13|22|39|47)|7(?:0[59]|78|8[02])|8(?:[06]7|19|25|73)|90[25])[2-9]\\d{6}","\\d{7}(?:\\d{3})?",,,"2042345678",,,[10]],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}|310\\d{4}","\\d{7}(?:\\d{3})?",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678",,,[10]],[,
,"NA","NA",,,,,,[-1]],"CA",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CC:[,[,,"[1458]\\d{5,9}","\\d{6,10}",,,,,,[6,7,9,10],[8]],[,,"89162\\d{4}","\\d{8,9}",,,"891621234",,,[9]],[,,"14(?:5\\d|71)\\d{5}|4(?:[0-2]\\d|3[0-57-9]|4[47-9]|5[0-25-9]|6[6-9]|7[02-9]|8[147-9]|9[017-9])\\d{6}","\\d{9}",,,"412345678",,,[9]],[,,"180(?:0\\d{3}|2)\\d{3}","\\d{7,10}",,,"1800123456",,,[7,10]],[,,"190[0126]\\d{6}","\\d{10}",,,"1900123456",
,,[10]],[,,"13(?:00\\d{2})?\\d{4}","\\d{6,10}",,,"1300123456",,,[6,10]],[,,"500\\d{6}","\\d{9}",,,"500123456",,,[9]],[,,"550\\d{6}","\\d{9}",,,"550123456",,,[9]],"CC",61,"(?:14(?:1[14]|34|4[17]|[56]6|7[47]|88))?001[14-689]","0",,,"0",,"0011",,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CD:[,[,,"[2-6]\\d{6}|[18]\\d{6,8}|9\\d{8}","\\d{7,9}",,,,,,[7,9]],[,,"1(?:2\\d{7}|\\d{6})|[2-6]\\d{6}","\\d{7,9}",,,"1234567"],[,,"8(?:[0-2459]\\d{2}|8)\\d{5}|9[017-9]\\d{7}",
"\\d{7,9}",,,"991234567"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"CD",243,"00","0",,,"0",,,,[[,"(\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["12"],"0$1"],[,"([89]\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["8[0-2459]|9"],"0$1"],[,"(\\d{2})(\\d{2})(\\d{3})","$1 $2 $3",["88"],"0$1"],[,"(\\d{2})(\\d{5})","$1 $2",["[1-6]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CF:[,
[,,"[278]\\d{7}","\\d{8}",,,,,,[8]],[,,"2[12]\\d{6}","\\d{8}",,,"21612345"],[,,"7[0257]\\d{6}","\\d{8}",,,"70012345"],[,,"NA","NA",,,,,,[-1]],[,,"8776\\d{4}","\\d{8}",,,"87761234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"CF",236,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CG:[,[,,"[028]\\d{8}","\\d{9}",,,,,,[9]],[,,"222[1-589]\\d{5}","\\d{9}",
,,"222123456"],[,,"0[14-6]\\d{7}","\\d{9}",,,"061234567"],[,,"NA","NA",,,,,,[-1]],[,,"800\\d{6}","\\d{9}",,,"800123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"CG",242,"00",,,,,,,,[[,"(\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["[02]"]],[,"(\\d)(\\d{4})(\\d{4})","$1 $2 $3",["8"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],CH:[,[,,"[2-9]\\d{8}|860\\d{9}","\\d{9}(?:\\d{3})?",,,,,,[9,12]],[,,"(?:2[12467]|3[1-4]|4[134]|5[256]|6[12]|[7-9]1)\\d{7}",
"\\d{9}",,,"212345678",,,[9]],[,,"7[5-9]\\d{7}","\\d{9}",,,"781234567",,,[9]],[,,"800\\d{6}","\\d{9}",,,"800123456",,,[9]],[,,"90[016]\\d{6}","\\d{9}",,,"900123456",,,[9]],[,,"84[0248]\\d{6}","\\d{9}",,,"840123456",,,[9]],[,,"878\\d{6}","\\d{9}",,,"878123456",,,[9]],[,,"NA","NA",,,,,,[-1]],"CH",41,"00","0",,,"0",,,,[[,"([2-9]\\d)(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[2-7]|[89]1"],"0$1"],[,"([89]\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["8[047]|90"],"0$1"],[,"(\\d{3})(\\d{2})(\\d{3})(\\d{2})(\\d{2})",
"$1 $2 $3 $4 $5",["860"],"0$1"]],,[,,"74[0248]\\d{6}","\\d{9}",,,"740123456",,,[9]],,,[,,"NA","NA",,,,,,[-1]],[,,"5[18]\\d{7}","\\d{9}",,,"581234567",,,[9]],,,[,,"860\\d{9}","\\d{12}",,,"860123456789",,,[12]]],CI:[,[,,"[02-8]\\d{7}","\\d{8}",,,,,,[8]],[,,"(?:2(?:0[023]|1[02357]|[23][045]|4[03-5])|3(?:0[06]|1[069]|[2-4][07]|5[09]|6[08]))\\d{5}","\\d{8}",,,"21234567"],[,,"(?:0[1-9]|4\\d|5[14-9]|6[015-79]|7[578]|8[79])\\d{6}","\\d{8}",,,"01234567"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,
"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"CI",225,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],CK:[,[,,"[2-8]\\d{4}","\\d{5}",,,,,,[5]],[,,"(?:2\\d|3[13-7]|4[1-5])\\d{3}","\\d{5}",,,"21234"],[,,"[5-8]\\d{4}","\\d{5}",,,"71234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"CK",
682,"00",,,,,,,,[[,"(\\d{2})(\\d{3})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CL:[,[,,"(?:[2-9]|600|123)\\d{7,8}","\\d{7,11}",,,,,,[9,10,11],[7,8]],[,,"2(?:1962\\d{4}|2\\d{7}|32[0-267]\\d{5})|(?:3[2-5]|[47][1-35]|5[1-3578]|6[13-57])\\d{7}","\\d{7,9}",,,"221234567",,,[9]],[,,"9[3-9]\\d{7}","\\d{8,9}",,,"961234567",,,[9]],[,,"800\\d{6}|1230\\d{7}","\\d{9,11}",,,"800123456",,,[9,11]],[,,"NA","NA",,,,,,[-1]],[,,"600\\d{7,8}","\\d{10,11}",
,,"6001234567",,,[10,11]],[,,"NA","NA",,,,,,[-1]],[,,"44\\d{7}","\\d{9}",,,"441234567",,,[9]],"CL",56,"(?:0|1(?:1[0-69]|2[0-57]|5[13-58]|69|7[0167]|8[018]))0","0",,,"0|(1(?:1[0-69]|2[0-57]|5[13-58]|69|7[0167]|8[018]))",,,,[[,"(\\d)(\\d{4})(\\d{4})","$1 $2 $3",["2[23]"],"($1)","$CC ($1)"],[,"(\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["[357]|4[1-35]|6[13-57]"],"($1)","$CC ($1)"],[,"(9)(\\d{4})(\\d{4})","$1 $2 $3",["9"],"0$1"],[,"(44)(\\d{3})(\\d{4})","$1 $2 $3",["44"],"0$1"],[,"([68]00)(\\d{3})(\\d{3,4})",
"$1 $2 $3",["60|8"],"$1"],[,"(600)(\\d{3})(\\d{2})(\\d{3})","$1 $2 $3 $4",["60"],"$1"],[,"(1230)(\\d{3})(\\d{4})","$1 $2 $3",["1"],"$1"],[,"(\\d{5})(\\d{4})","$1 $2",["219"],"($1)","$CC ($1)"],[,"(\\d{4,5})","$1",["[1-9]"],"$1"]],[[,"(\\d)(\\d{4})(\\d{4})","$1 $2 $3",["2[23]"],"($1)","$CC ($1)"],[,"(\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["[357]|4[1-35]|6[13-57]"],"($1)","$CC ($1)"],[,"(9)(\\d{4})(\\d{4})","$1 $2 $3",["9"],"0$1"],[,"(44)(\\d{3})(\\d{4})","$1 $2 $3",["44"],"0$1"],[,"([68]00)(\\d{3})(\\d{3,4})",
"$1 $2 $3",["60|8"],"$1"],[,"(600)(\\d{3})(\\d{2})(\\d{3})","$1 $2 $3 $4",["60"],"$1"],[,"(1230)(\\d{3})(\\d{4})","$1 $2 $3",["1"],"$1"],[,"(\\d{5})(\\d{4})","$1 $2",["219"],"($1)","$CC ($1)"]],[,,"NA","NA",,,,,,[-1]],,,[,,"600\\d{7,8}","\\d{10,11}",,,"6001234567",,,[10,11]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CM:[,[,,"[2368]\\d{7,8}","\\d{8,9}",,,,,,[8,9]],[,,"2(?:22|33|4[23])\\d{6}","\\d{9}",,,"222123456",,,[9]],[,,"6[5-9]\\d{7}","\\d{9}",,,"671234567",,,[9]],[,,"800\\d{5}","\\d{8}",
,,"80012345",,,[8]],[,,"88\\d{6}","\\d{8}",,,"88012345",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"CM",237,"00",,,,,,,,[[,"([26])(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4 $5",["[26]"]],[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[23]|88"]],[,"(800)(\\d{2})(\\d{3})","$1 $2 $3",["80"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CN:[,[,,"[1-7]\\d{6,11}|8[0-357-9]\\d{6,9}|9\\d{7,10}","\\d{4,12}",
,,,,,[7,8,9,10,11,12],[5,6]],[,,"21(?:100\\d{2}|95\\d{3,4}|\\d{8,10})|(?:10|2[02-57-9]|3(?:11|7[179])|4(?:[15]1|3[1-35])|5(?:1\\d|2[37]|3[12]|51|7[13-79]|9[15])|7(?:31|5[457]|6[09]|91)|8(?:[57]1|98))(?:100\\d{2}|95\\d{3,4}|\\d{8})|(?:3(?:1[02-9]|35|49|5\\d|7[02-68]|9[1-68])|4(?:1[02-9]|2[179]|3[3-9]|5[2-9]|6[4789]|7\\d|8[23])|5(?:3[03-9]|4[36]|5[02-9]|6[1-46]|7[028]|80|9[2-46-9])|6(?:3[1-5]|6[0238]|9[12])|7(?:01|[17]\\d|2[248]|3[04-9]|4[3-6]|5[0-4689]|6[2368]|9[02-9])|8(?:078|1[236-8]|2[5-7]|3\\d|5[1-9]|7[02-9]|8[3678]|9[1-7])|9(?:0[1-3689]|1[1-79]|[379]\\d|4[13]|5[1-5]))(?:100\\d{2}|95\\d{3,4}|\\d{7})|80(?:29|6[03578]|7[018]|81)\\d{4}",
"\\d{4,12}",,,"1012345678"],[,,"1(?:[38]\\d|4[57]|5[0-35-9]|7[0136-8])\\d{8}","\\d{11}",,,"13123456789",,,[11]],[,,"(?:10)?800\\d{7}","\\d{10,12}",,,"8001234567",,,[10,12]],[,,"16[08]\\d{5}","\\d{8}",,,"16812345",,,[8]],[,,"400\\d{7}|950\\d{7,8}|(?:10|2[0-57-9]|3(?:[157]\\d|35|49|9[1-68])|4(?:[17]\\d|2[179]|[35][1-9]|6[4789]|8[23])|5(?:[1357]\\d|2[37]|4[36]|6[1-46]|80|9[1-9])|6(?:3[1-5]|6[0238]|9[12])|7(?:01|[1579]\\d|2[248]|3[014-9]|4[3-6]|6[023689])|8(?:1[236-8]|2[5-7]|[37]\\d|5[14-9]|8[3678]|9[1-8])|9(?:0[1-3689]|1[1-79]|[379]\\d|4[13]|5[1-5]))96\\d{3,4}",
"\\d{7,11}",,,"4001234567",,,[7,8,9,10,11]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"CN",86,"(1(?:[129]\\d{3}|79\\d{2}))?00","0",,,"(1(?:[129]\\d{3}|79\\d{2}))|0",,"00",,[[,"(80\\d{2})(\\d{4})","$1 $2",["80[2678]"],"0$1","$CC $1",1],[,"([48]00)(\\d{3})(\\d{4})","$1 $2 $3",["[48]00"]],[,"(\\d{5,6})","$1",["100|95"]],[,"(\\d{2})(\\d{5,6})","$1 $2",["(?:10|2\\d)[19]","(?:10|2\\d)(?:10|9[56])","(?:10|2\\d)(?:100|9[56])"],"0$1","$CC $1"],[,"(\\d{3})(\\d{5,6})","$1 $2",["[3-9]","[3-9]\\d{2}[19]",
"[3-9]\\d{2}(?:10|9[56])"],"0$1","$CC $1"],[,"(\\d{3,4})(\\d{4})","$1 $2",["[2-9]"]],[,"(21)(\\d{4})(\\d{4,6})","$1 $2 $3",["21"],"0$1","$CC $1",1],[,"([12]\\d)(\\d{4})(\\d{4})","$1 $2 $3",["10[1-9]|2[02-9]","10[1-9]|2[02-9]","10(?:[1-79]|8(?:[1-9]|0[1-9]))|2[02-9]"],"0$1","$CC $1",1],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["3(?:1[02-9]|35|49|5|7[02-68]|9[1-68])|4(?:1[02-9]|2[179]|[35][2-9]|6[4789]|7\\d|8[23])|5(?:3[03-9]|4[36]|5[02-9]|6[1-46]|7[028]|80|9[2-46-9])|6(?:3[1-5]|6[0238]|9[12])|7(?:01|[1579]|2[248]|3[04-9]|4[3-6]|6[2368])|8(?:1[236-8]|2[5-7]|3|5[1-9]|7[02-9]|8[3678]|9[1-7])|9(?:0[1-3689]|1[1-79]|[379]|4[13]|5[1-5])"],
"0$1","$CC $1",1],[,"(\\d{3})(\\d{4})(\\d{4})","$1 $2 $3",["3(?:11|7[179])|4(?:[15]1|3[1-35])|5(?:1|2[37]|3[12]|51|7[13-79]|9[15])|7(?:31|5[457]|6[09]|91)|8(?:[57]1|98)"],"0$1","$CC $1",1],[,"(\\d{4})(\\d{3})(\\d{4})","$1 $2 $3",["807","8078"],"0$1","$CC $1",1],[,"(\\d{3})(\\d{4})(\\d{4})","$1 $2 $3",["1[3-578]"],,"$CC $1"],[,"(10800)(\\d{3})(\\d{4})","$1 $2 $3",["108","1080","10800"]],[,"(\\d{3})(\\d{7,8})","$1 $2",["950"]]],[[,"(80\\d{2})(\\d{4})","$1 $2",["80[2678]"],"0$1","$CC $1",1],[,"([48]00)(\\d{3})(\\d{4})",
"$1 $2 $3",["[48]00"]],[,"(\\d{2})(\\d{5,6})","$1 $2",["(?:10|2\\d)[19]","(?:10|2\\d)(?:10|9[56])","(?:10|2\\d)(?:100|9[56])"],"0$1","$CC $1"],[,"(\\d{3})(\\d{5,6})","$1 $2",["[3-9]","[3-9]\\d{2}[19]","[3-9]\\d{2}(?:10|9[56])"],"0$1","$CC $1"],[,"(21)(\\d{4})(\\d{4,6})","$1 $2 $3",["21"],"0$1","$CC $1",1],[,"([12]\\d)(\\d{4})(\\d{4})","$1 $2 $3",["10[1-9]|2[02-9]","10[1-9]|2[02-9]","10(?:[1-79]|8(?:[1-9]|0[1-9]))|2[02-9]"],"0$1","$CC $1",1],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["3(?:1[02-9]|35|49|5|7[02-68]|9[1-68])|4(?:1[02-9]|2[179]|[35][2-9]|6[4789]|7\\d|8[23])|5(?:3[03-9]|4[36]|5[02-9]|6[1-46]|7[028]|80|9[2-46-9])|6(?:3[1-5]|6[0238]|9[12])|7(?:01|[1579]|2[248]|3[04-9]|4[3-6]|6[2368])|8(?:1[236-8]|2[5-7]|3|5[1-9]|7[02-9]|8[3678]|9[1-7])|9(?:0[1-3689]|1[1-79]|[379]|4[13]|5[1-5])"],
"0$1","$CC $1",1],[,"(\\d{3})(\\d{4})(\\d{4})","$1 $2 $3",["3(?:11|7[179])|4(?:[15]1|3[1-35])|5(?:1|2[37]|3[12]|51|7[13-79]|9[15])|7(?:31|5[457]|6[09]|91)|8(?:[57]1|98)"],"0$1","$CC $1",1],[,"(\\d{4})(\\d{3})(\\d{4})","$1 $2 $3",["807","8078"],"0$1","$CC $1",1],[,"(\\d{3})(\\d{4})(\\d{4})","$1 $2 $3",["1[3-578]"],,"$CC $1"],[,"(10800)(\\d{3})(\\d{4})","$1 $2 $3",["108","1080","10800"]],[,"(\\d{3})(\\d{7,8})","$1 $2",["950"]]],[,,"NA","NA",,,,,,[-1]],,,[,,"(?:4|(?:10)?8)00\\d{7}|950\\d{7,8}","\\d{10,12}",
,,"4001234567",,,[10,11,12]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CO:[,[,,"(?:[13]\\d{0,3}|[24-8])\\d{7}","\\d{7,11}",,,,,,[8,10,11],[7]],[,,"[124-8][2-9]\\d{6}","\\d{8}",,,"12345678",,,[8]],[,,"3(?:0[0-5]|1\\d|2[0-3]|5[01])\\d{7}","\\d{10}",,,"3211234567",,,[10]],[,,"1800\\d{7}","\\d{11}",,,"18001234567",,,[11]],[,,"19(?:0[01]|4[78])\\d{7}","\\d{11}",,,"19001234567",,,[11]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"CO",57,"00(?:4(?:[14]4|56)|[579])","0",
,,"0([3579]|4(?:44|56))?",,,,[[,"(\\d)(\\d{7})","$1 $2",["1(?:8[2-9]|9[0-3]|[2-7])|[24-8]","1(?:8[2-9]|9(?:09|[1-3])|[2-7])|[24-8]"],"($1)","0$CC $1"],[,"(\\d{3})(\\d{7})","$1 $2",["3"],,"0$CC $1"],[,"(1)(\\d{3})(\\d{7})","$1-$2-$3",["1(?:80|9[04])","1(?:800|9(?:0[01]|4[78]))"],"0$1"]],[[,"(\\d)(\\d{7})","$1 $2",["1(?:8[2-9]|9[0-3]|[2-7])|[24-8]","1(?:8[2-9]|9(?:09|[1-3])|[2-7])|[24-8]"],"($1)","0$CC $1"],[,"(\\d{3})(\\d{7})","$1 $2",["3"],,"0$CC $1"],[,"(1)(\\d{3})(\\d{7})","$1 $2 $3",["1(?:80|9[04])",
"1(?:800|9(?:0[01]|4[78]))"]]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CR:[,[,,"[24-9]\\d{7,9}","\\d{8,10}",,,,,,[8,10]],[,,"2[0-24-7]\\d{6}","\\d{8}",,,"22123456",,,[8]],[,,"5(?:0[01]|7[0-3])\\d{5}|(?:[67][0-3]|8[3-9])\\d{6}","\\d{8}",,,"83123456",,,[8]],[,,"800\\d{7}","\\d{10}",,,"8001234567",,,[10]],[,,"90[059]\\d{7}","\\d{10}",,,"9001234567",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"210[0-6]\\d{4}|4\\d{7}|5100\\d{4}",
"\\d{8}",,,"40001234",,,[8]],"CR",506,"00",,,,"(19(?:0[012468]|1[09]|20|66|77|99))",,,,[[,"(\\d{4})(\\d{4})","$1 $2",["[24-7]|8[3-9]"],,"$CC $1"],[,"(\\d{3})(\\d{3})(\\d{4})","$1-$2-$3",["[89]0"],,"$CC $1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CU:[,[,,"[2-57]\\d{5,7}","\\d{4,8}",,,,,,[6,7,8],[4,5]],[,,"2[1-4]\\d{5,6}|3(?:1\\d{6}|[23]\\d{4,6})|4(?:[125]\\d{5,6}|[36]\\d{6}|[78]\\d{4,6})|7\\d{6,7}","\\d{4,8}",,,"71234567"],[,,"5\\d{7}",
"\\d{8}",,,"51234567",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"CU",53,"119","0",,,"0",,,,[[,"(\\d)(\\d{6,7})","$1 $2",["7"],"(0$1)"],[,"(\\d{2})(\\d{4,6})","$1 $2",["[2-4]"],"(0$1)"],[,"(\\d)(\\d{7})","$1 $2",["5"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CV:[,[,,"[259]\\d{6}","\\d{7}",,,,,,[7]],[,,"2(?:2[1-7]|3[0-8]|4[12]|5[1256]|6\\d|7[1-3]|8[1-5])\\d{4}",
"\\d{7}",,,"2211234"],[,,"(?:9\\d|59)\\d{5}","\\d{7}",,,"9911234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"CV",238,"0",,,,,,,,[[,"(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CW:[,[,,"[169]\\d{6,7}","\\d{7,8}",,,,,,[7,8]],[,,"9(?:[48]\\d{2}|50\\d|7(?:2[0-24]|[34]\\d|6[35-7]|77|8[7-9]))\\d{4}","\\d{7,8}",,,"94151234",,,[8]],
[,,"9(?:5(?:[12467]\\d|3[01])|6(?:[15-9]\\d|3[01]))\\d{4}","\\d{7,8}",,,"95181234",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"60[0-2]\\d{4}","\\d{7}",,,"6001234",,,[7]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"CW",599,"00",,,,,,,,[[,"(\\d{3})(\\d{4})","$1 $2",["[13-7]"]],[,"(9)(\\d{3})(\\d{4})","$1 $2 $3",["9"]]],,[,,"955\\d{5}","\\d{7,8}",,,"95581234",,,[8]],1,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CX:[,[,,"[1458]\\d{5,9}","\\d{6,10}",
,,,,,[6,7,8,9,10]],[,,"89164\\d{4}","\\d{8,9}",,,"891641234",,,[9]],[,,"14(?:5\\d|71)\\d{5}|4(?:[0-2]\\d|3[0-57-9]|4[47-9]|5[0-25-9]|6[6-9]|7[02-9]|8[147-9]|9[017-9])\\d{6}","\\d{9}",,,"412345678",,,[9]],[,,"180(?:0\\d{3}|2)\\d{3}","\\d{7,10}",,,"1800123456",,,[7,10]],[,,"190[0126]\\d{6}","\\d{10}",,,"1900123456",,,[10]],[,,"13(?:00\\d{2})?\\d{4}","\\d{6,10}",,,"1300123456",,,[6,8,10]],[,,"500\\d{6}","\\d{9}",,,"500123456",,,[9]],[,,"550\\d{6}","\\d{9}",,,"550123456",,,[9]],"CX",61,"(?:14(?:1[14]|34|4[17]|[56]6|7[47]|88))?001[14-689]",
"0",,,"0",,"0011",,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],CY:[,[,,"[257-9]\\d{7}","\\d{8}",,,,,,[8]],[,,"2[2-6]\\d{6}","\\d{8}",,,"22345678"],[,,"9[4-79]\\d{6}","\\d{8}",,,"96123456"],[,,"800\\d{5}","\\d{8}",,,"80001234"],[,,"90[09]\\d{5}","\\d{8}",,,"90012345"],[,,"80[1-9]\\d{5}","\\d{8}",,,"80112345"],[,,"700\\d{5}","\\d{8}",,,"70012345"],[,,"NA","NA",,,,,,[-1]],"CY",357,"00",,,,,,,,[[,"(\\d{2})(\\d{6})","$1 $2"]],,[,,"NA","NA",,,,
,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"(?:50|77)\\d{6}","\\d{8}",,,"77123456"],,,[,,"NA","NA",,,,,,[-1]]],CZ:[,[,,"[2-8]\\d{8}|9\\d{8,11}","\\d{9,12}",,,,,,[9,10,11,12]],[,,"2\\d{8}|(?:3[1257-9]|4[16-9]|5[13-9])\\d{7}","\\d{9,12}",,,"212345678",,,[9]],[,,"(?:60[1-8]|7(?:0[2-5]|[2379]\\d))\\d{6}","\\d{9,12}",,,"601123456",,,[9]],[,,"800\\d{6}","\\d{9,12}",,,"800123456",,,[9]],[,,"9(?:0[05689]|76)\\d{6}","\\d{9,12}",,,"900123456",,,[9]],[,,"8[134]\\d{7}","\\d{9,12}",,,"811234567",,,[9]],[,,"70[01]\\d{6}",
"\\d{9,12}",,,"700123456",,,[9]],[,,"9[17]0\\d{6}","\\d{9,12}",,,"910123456",,,[9]],"CZ",420,"00",,,,,,,,[[,"([2-9]\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["[2-8]|9[015-7]"]],[,"(96\\d)(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3 $4",["96"]],[,"(9\\d)(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3 $4",["9[36]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"9(?:5\\d|7[234])\\d{6}","\\d{9,12}",,,"972123456",,,[9]],,,[,,"9(?:3\\d{9}|6\\d{7,10})","\\d{9,12}",,,"93123456789"]],DE:[,[,,"[1-35-9]\\d{3,14}|4(?:[0-8]\\d{4,12}|9(?:[0-37]\\d|4(?:[1-35-8]|4\\d?)|5\\d{1,2}|6[1-8]\\d?)\\d{2,8})",
"\\d{2,15}",,,,,,[4,5,6,7,8,9,10,11,12,13,14,15],[3]],[,,"[246]\\d{5,13}|3(?:0\\d{3,13}|2\\d{9}|[3-9]\\d{4,13})|5(?:0[2-8]|[1256]\\d|[38][0-8]|4\\d{0,2}|[79][0-7])\\d{3,11}|7(?:0[2-8]|[1-9]\\d)\\d{3,10}|8(?:0[2-9]|[1-9]\\d)\\d{3,10}|9(?:0[6-9]\\d{3,10}|1\\d{4,12}|[2-9]\\d{4,11})","\\d{2,15}",,,"30123456",,,[5,6,7,8,9,10,11,12,13,14,15]],[,,"1(?:5[0-25-9]\\d{8}|6[023]\\d{7,8}|7(?:[0-57-9]\\d?|6\\d)\\d{7})","\\d{10,11}",,,"15123456789",,,[10,11]],[,,"800\\d{7,12}","\\d{10,15}",,,"8001234567890",,,[10,
11,12,13,14,15]],[,,"137[7-9]\\d{6}|900(?:[135]\\d{6}|9\\d{7})","\\d{10,11}",,,"9001234567",,,[10,11]],[,,"1(?:3(?:7[1-6]\\d{6}|8\\d{4})|80\\d{5,11})","\\d{7,14}",,,"18012345",,,[7,8,9,10,11,12,13,14]],[,,"700\\d{8}","\\d{11}",,,"70012345678",,,[11]],[,,"NA","NA",,,,,,[-1]],"DE",49,"00","0",,,"0",,,,[[,"(1\\d{2})(\\d{7,8})","$1 $2",["1[67]"],"0$1"],[,"(15\\d{3})(\\d{6})","$1 $2",["15[0568]"],"0$1"],[,"(1\\d{3})(\\d{7})","$1 $2",["15"],"0$1"],[,"(\\d{2})(\\d{3,11})","$1 $2",["3[02]|40|[68]9"],"0$1"],
[,"(\\d{3})(\\d{3,11})","$1 $2",["2(?:\\d1|0[2389]|1[24]|28|34)|3(?:[3-9][15]|40)|[4-8][1-9]1|9(?:06|[1-9]1)"],"0$1"],[,"(\\d{4})(\\d{2,11})","$1 $2",["[24-6]|[7-9](?:\\d[1-9]|[1-9]\\d)|3(?:[3569][02-46-9]|4[2-4679]|7[2-467]|8[2-46-8])","[24-6]|[7-9](?:\\d[1-9]|[1-9]\\d)|3(?:3(?:0[1-467]|2[127-9]|3[124578]|[46][1246]|7[1257-9]|8[1256]|9[145])|4(?:2[135]|3[1357]|4[13578]|6[1246]|7[1356]|9[1346])|5(?:0[14]|2[1-3589]|3[1357]|4[1246]|6[1-4]|7[1346]|8[13568]|9[1246])|6(?:0[356]|2[1-489]|3[124-6]|4[1347]|6[13]|7[12579]|8[1-356]|9[135])|7(?:2[1-7]|3[1357]|4[145]|6[1-5]|7[1-4])|8(?:21|3[1468]|4[1347]|6[0135-9]|7[1467]|8[136])|9(?:0[12479]|2[1358]|3[1357]|4[134679]|6[1-9]|7[136]|8[147]|9[1468]))"],
"0$1"],[,"(3\\d{4})(\\d{1,10})","$1 $2",["3"],"0$1"],[,"(800)(\\d{7,12})","$1 $2",["800"],"0$1"],[,"(\\d{3})(\\d)(\\d{4,10})","$1 $2 $3",["(?:18|90)0|137","1(?:37|80)|900[1359]"],"0$1"],[,"(1\\d{2})(\\d{5,11})","$1 $2",["181"],"0$1"],[,"(18\\d{3})(\\d{6})","$1 $2",["185","1850","18500"],"0$1"],[,"(18\\d{2})(\\d{7})","$1 $2",["18[68]"],"0$1"],[,"(18\\d)(\\d{8})","$1 $2",["18[2-579]"],"0$1"],[,"(700)(\\d{4})(\\d{4})","$1 $2 $3",["700"],"0$1"],[,"(138)(\\d{4})","$1 $2",["138"],"0$1"],[,"(15[013-68])(\\d{2})(\\d{8})",
"$1 $2 $3",["15[013-68]"],"0$1"],[,"(15[279]\\d)(\\d{2})(\\d{7})","$1 $2 $3",["15[279]"],"0$1"],[,"(1[67]\\d)(\\d{2})(\\d{7,8})","$1 $2 $3",["1(?:6[023]|7)"],"0$1"]],,[,,"16(?:4\\d{1,10}|[89]\\d{1,11})","\\d{4,14}",,,"16412345",,,[4,5,6,7,8,9,10,11,12,13,14]],,,[,,"NA","NA",,,,,,[-1]],[,,"18(?:1\\d{5,11}|[2-9]\\d{8})","\\d{8,14}",,,"18500123456",,,[8,9,10,11,12,13,14]],,,[,,"1(?:5(?:(?:2\\d55|7\\d99|9\\d33)\\d{7}|(?:[034568]00|113)\\d{8})|6(?:013|255|399)\\d{7,8}|7(?:[015]13|[234]55|[69]33|[78]99)\\d{7,8})",
"\\d{12,13}",,,"177991234567",,,[12,13]]],DJ:[,[,,"[27]\\d{7}","\\d{8}",,,,,,[8]],[,,"2(?:1[2-5]|7[45])\\d{5}","\\d{8}",,,"21360003"],[,,"77[0-26-8]\\d{5}","\\d{8}",,,"77831001"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"DJ",253,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],DK:[,[,,"[2-9]\\d{7}","\\d{8}",
,,,,,[8]],[,,"(?:[2-7]\\d|8[126-9]|9[1-36-9])\\d{6}","\\d{8}",,,"32123456"],[,,"(?:[2-7]\\d|8[126-9]|9[1-36-9])\\d{6}","\\d{8}",,,"20123456"],[,,"80\\d{6}","\\d{8}",,,"80123456"],[,,"90\\d{6}","\\d{8}",,,"90123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"DK",45,"00",,,,,,,1,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],DM:[,[,,"[57-9]\\d{9}","\\d{7}(?:\\d{3})?",
,,,,,[10],[7]],[,,"767(?:2(?:55|66)|4(?:2[01]|4[0-25-9])|50[0-4]|70[1-3])\\d{4}","\\d{7}(?:\\d{3})?",,,"7674201234"],[,,"767(?:2(?:[234689]5|7[5-7])|31[5-7]|61[2-7])\\d{4}","\\d{7}(?:\\d{3})?",,,"7672251234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"DM",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"767",[,,"NA",
"NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],DO:[,[,,"[589]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"8(?:[04]9[2-9]\\d{6}|29(?:2(?:[0-59]\\d|6[04-9]|7[0-27]|8[0237-9])|3(?:[0-35-9]\\d|4[7-9])|[45]\\d{2}|6(?:[0-27-9]\\d|[3-5][1-9]|6[0135-8])|7(?:0[013-9]|[1-37]\\d|4[1-35689]|5[1-4689]|6[1-57-9]|8[1-79]|9[1-8])|8(?:0[146-9]|1[0-48]|[248]\\d|3[1-79]|5[01589]|6[013-68]|7[124-8]|9[0-8])|9(?:[0-24]\\d|3[02-46-9]|5[0-79]|60|7[0169]|8[57-9]|9[02-9]))\\d{4})","\\d{7}(?:\\d{3})?",
,,"8092345678"],[,,"8[024]9[2-9]\\d{6}","\\d{7}(?:\\d{3})?",,,"8092345678"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"DO",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"8[024]9",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],DZ:[,[,,"(?:[1-4]|[5-9]\\d)\\d{7}","\\d{8,9}",,,,,,[8,9]],[,
,"(?:1\\d|2[013-79]|3[0-8]|4[0135689])\\d{6}|9619\\d{5}","\\d{8,9}",,,"12345678"],[,,"(?:5[4-6]|7[7-9])\\d{7}|6(?:[569]\\d|7[0-6])\\d{6}","\\d{9}",,,"551234567",,,[9]],[,,"800\\d{6}","\\d{9}",,,"800123456",,,[9]],[,,"80[3-689]1\\d{5}","\\d{9}",,,"808123456",,,[9]],[,,"80[12]1\\d{5}","\\d{9}",,,"801123456",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"98[23]\\d{6}","\\d{9}",,,"983123456",,,[9]],"DZ",213,"00","0",,,"0",,,,[[,"([1-4]\\d)(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[1-4]"],"0$1"],[,"([5-8]\\d{2})(\\d{2})(\\d{2})(\\d{2})",
"$1 $2 $3 $4",["[5-8]"],"0$1"],[,"(9\\d)(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3 $4",["9"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],EC:[,[,,"1\\d{9,10}|[2-8]\\d{7}|9\\d{8}","\\d{7,11}",,,,,,[8,9,10,11],[7]],[,,"[2-7][2-7]\\d{6}","\\d{7,8}",,,"22123456",,,[8]],[,,"9(?:(?:39|[45][89]|7[7-9]|[89]\\d)\\d|6(?:[017-9]\\d|2[0-4]))\\d{5}","\\d{9}",,,"991234567",,,[9]],[,,"1800\\d{6,7}","\\d{10,11}",,,"18001234567",,,[10,11]],[,,"NA","NA",,,,
,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"[2-7]890\\d{4}","\\d{8}",,,"28901234",,,[8]],"EC",593,"00","0",,,"0",,,,[[,"(\\d)(\\d{3})(\\d{4})","$1 $2-$3",["[247]|[356][2-8]"],"(0$1)"],[,"(\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["9"],"0$1"],[,"(1800)(\\d{3})(\\d{3,4})","$1 $2 $3",["1"],"$1"]],[[,"(\\d)(\\d{3})(\\d{4})","$1-$2-$3",["[247]|[356][2-8]"]],[,"(\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["9"],"0$1"],[,"(1800)(\\d{3})(\\d{3,4})","$1 $2 $3",["1"],"$1"]],[,,"NA","NA",,,,,,[-1]],,,[,,
"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],EE:[,[,,"1\\d{3,4}|[3-9]\\d{6,7}|800\\d{6,7}","\\d{4,10}",,,,,,[4,5,7,8,10]],[,,"(?:3[23589]|4[3-8]|6\\d|7[1-9]|88)\\d{5}","\\d{7}",,,"3212345",,,[7]],[,,"(?:5\\d|8[1-5])\\d{6}|5(?:[02]\\d{2}|1(?:[0-8]\\d|95)|5[0-478]\\d|64[0-4]|65[1-589])\\d{3}","\\d{7,8}",,,"51234567",,,[7,8]],[,,"800(?:0\\d{3}|1\\d|[2-9])\\d{3}","\\d{7,10}",,,"80012345",,,[7,8,10]],[,,"(?:40\\d{2}|900)\\d{4}","\\d{7,8}",,,"9001234",,,[7,8]],[,,"NA","NA",,,
,,,[-1]],[,,"70[0-2]\\d{5}","\\d{8}",,,"70012345",,,[8]],[,,"NA","NA",,,,,,[-1]],"EE",372,"00",,,,,,,,[[,"([3-79]\\d{2})(\\d{4})","$1 $2",["[369]|4[3-8]|5(?:[0-2]|5[0-478]|6[45])|7[1-9]","[369]|4[3-8]|5(?:[02]|1(?:[0-8]|95)|5[0-478]|6(?:4[0-4]|5[1-589]))|7[1-9]"]],[,"(70)(\\d{2})(\\d{4})","$1 $2 $3",["70"]],[,"(8000)(\\d{3})(\\d{3})","$1 $2 $3",["800","8000"]],[,"([458]\\d{3})(\\d{3,4})","$1 $2",["40|5|8(?:00|[1-5])","40|5|8(?:00[1-9]|[1-5])"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"1\\d{3,4}|800[2-9]\\d{3}",
"\\d{4,7}",,,"8002123",,,[4,5,7]],[,,"1(?:2[01245]|3[0-6]|4[1-489]|5[0-59]|6[1-46-9]|7[0-27-9]|8[189]|9[012])\\d{1,2}","\\d{4,5}",,,"12123",,,[4,5]],,,[,,"NA","NA",,,,,,[-1]]],EG:[,[,,"1\\d{4,9}|[2456]\\d{8}|3\\d{7}|[89]\\d{8,9}","\\d{5,10}",,,,,,[5,8,9,10],[7]],[,,"(?:1(?:3[23]\\d|5(?:[23]|9\\d))|2[2-4]\\d{2}|3\\d{2}|4(?:0[2-5]|[578][23]|64)\\d|5(?:0[2-7]|[57][23])\\d|6[24-689]3\\d|8(?:2[2-57]|4[26]|6[237]|8[2-4])\\d|9(?:2[27]|3[24]|52|6[2356]|7[2-4])\\d)\\d{5}|1[69]\\d{3}","\\d{5,9}",,,"234567890",
,,[5,8,9]],[,,"1(?:0[0-269]|1[0-245]|2[0-278])\\d{7}","\\d{10}",,,"1001234567",,,[10]],[,,"800\\d{7}","\\d{10}",,,"8001234567",,,[10]],[,,"900\\d{7}","\\d{10}",,,"9001234567",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"EG",20,"00","0",,,"0",,,,[[,"(\\d)(\\d{7,8})","$1 $2",["[23]"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["1[012]|[89]00"],"0$1"],[,"(\\d{2})(\\d{6,7})","$1 $2",["1[35]|[4-6]|[89][2-9]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,
[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],EH:[,[,,"[5-9]\\d{8}","\\d{9}",,,,,,[9]],[,,"528[89]\\d{5}","\\d{9}",,,"528812345"],[,,"(?:6(?:[0-79]\\d|8[0-247-9])|7(?:[07][07]|6[12]))\\d{6}","\\d{9}",,,"650123456"],[,,"80\\d{7}","\\d{9}",,,"801234567"],[,,"89\\d{7}","\\d{9}",,,"891234567"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"5924[01]\\d{4}","\\d{9}",,,"592401234"],"EH",212,"00","0",,,"0",,,,,,[,,"NA","NA",,,,,,[-1]],,"528[89]",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],
,,[,,"NA","NA",,,,,,[-1]]],ER:[,[,,"[178]\\d{6}","\\d{6,7}",,,,,,[7],[6]],[,,"1(?:1[12568]|20|40|55|6[146])\\d{4}|8\\d{6}","\\d{6,7}",,,"8370362"],[,,"17[1-3]\\d{4}|7\\d{6}","\\d{7}",,,"7123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"ER",291,"00","0",,,"0",,,,[[,"(\\d)(\\d{3})(\\d{3})","$1 $2 $3",,"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],ES:[,[,,"[5-9]\\d{8}",
"\\d{9}",,,,,,[9]],[,,"8(?:[13]0|[28][0-8]|[47][1-9]|5[01346-9]|6[0457-9])\\d{6}|9(?:[1238][0-8]\\d{6}|4[1-9]\\d{6}|5\\d{7}|6(?:[0-8]\\d{6}|9(?:0(?:[0-57-9]\\d{4}|6(?:0[0-8]|1[1-9]|[2-9]\\d)\\d{2})|[1-9]\\d{5}))|7(?:[124-9]\\d{2}|3(?:[0-8]\\d|9[1-9]))\\d{4})","\\d{9}",,,"810123456"],[,,"(?:6\\d{6}|7[1-4]\\d{5}|9(?:6906(?:09|10)|7390\\d{2}))\\d{2}","\\d{9}",,,"612345678"],[,,"[89]00\\d{6}","\\d{9}",,,"800123456"],[,,"80[367]\\d{6}","\\d{9}",,,"803123456"],[,,"90[12]\\d{6}","\\d{9}",,,"901123456"],
[,,"70\\d{7}","\\d{9}",,,"701234567"],[,,"NA","NA",,,,,,[-1]],"ES",34,"00",,,,,,,,[[,"([89]00)(\\d{3})(\\d{3})","$1 $2 $3",["[89]00"]],[,"([5-9]\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[568]|[79][0-8]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"51\\d{7}","\\d{9}",,,"511234567"],,,[,,"NA","NA",,,,,,[-1]]],ET:[,[,,"[1-59]\\d{8}","\\d{7,9}",,,,,,[9],[7]],[,,"(?:11(?:1(?:1[124]|2[2-57]|3[1-5]|5[5-8]|8[6-8])|2(?:13|3[6-8]|5[89]|7[05-9]|8[2-6])|3(?:2[01]|3[0-289]|4[1289]|7[1-4]|87)|4(?:1[69]|3[2-49]|4[0-3]|6[5-8])|5(?:1[578]|44|5[0-4])|6(?:18|2[69]|39|4[5-7]|5[1-5]|6[0-59]|8[015-8]))|2(?:2(?:11[1-9]|22[0-7]|33\\d|44[1467]|66[1-68])|5(?:11[124-6]|33[2-8]|44[1467]|55[14]|66[1-3679]|77[124-79]|880))|3(?:3(?:11[0-46-8]|22[0-6]|33[0134689]|44[04]|55[0-6]|66[01467])|4(?:44[0-8]|55[0-69]|66[0-3]|77[1-5]))|4(?:6(?:22[0-24-7]|33[1-5]|44[13-69]|55[14-689]|660|88[1-4])|7(?:11[1-9]|22[1-9]|33[13-7]|44[13-6]|55[1-689]))|5(?:7(?:227|55[05]|(?:66|77)[14-8])|8(?:11[149]|22[013-79]|33[0-68]|44[013-8]|550|66[1-5]|77\\d)))\\d{4}",
"\\d{7,9}",,,"111112345"],[,,"9(?:[1-468]\\d|5[89])\\d{6}","\\d{9}",,,"911234567"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"ET",251,"00","0",,,"0",,,,[[,"([1-59]\\d)(\\d{3})(\\d{4})","$1 $2 $3",,"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],FI:[,[,,"1\\d{4,11}|[2-9]\\d{4,10}","\\d{5,12}",,,,,,[5,6,7,8,9,10,11,12]],[,,"1(?:[3569][1-8]\\d{3,9}|[47]\\d{5,10})|2[1-8]\\d{3,9}|3(?:[1-8]\\d{3,9}|9\\d{4,8})|[5689][1-8]\\d{3,9}",
"\\d{5,12}",,,"1312345678"],[,,"4\\d{5,10}|50\\d{4,8}","\\d{6,11}",,,"412345678",,,[6,7,8,9,10,11]],[,,"800\\d{4,7}","\\d{7,10}",,,"8001234567",,,[7,8,9,10]],[,,"[67]00\\d{5,6}","\\d{8,9}",,,"600123456",,,[8,9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"FI",358,"00|99(?:[02469]|5(?:11|33|5[59]|88|9[09]))","0",,,"0",,"00",,[[,"(\\d{3})(\\d{3,7})","$1 $2",["(?:[1-3]00|[6-8]0)"],"0$1"],[,"(116\\d{3})","$1",["116"],"$1"],[,"(\\d{2})(\\d{4,10})","$1 $2",["[14]|2[09]|50|7[135]"],
"0$1"],[,"(\\d)(\\d{4,11})","$1 $2",["[25689][1-8]|3"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],1,,[,,"[13]00\\d{3,7}|2(?:0(?:0\\d{3,7}|2[023]\\d{1,6}|9[89]\\d{1,6}))|60(?:[12]\\d{5,6}|6\\d{7})|7(?:1\\d{7}|3\\d{8}|5[03-9]\\d{2,7})","\\d{5,10}",,,"100123",,,[5,6,7,8,9,10]],[,,"[13]0\\d{4,8}|2(?:0(?:[016-8]\\d{3,7}|[2-59]\\d{2,7})|9\\d{4,8})|60(?:[12]\\d{5,6}|6\\d{7})|7(?:1\\d{7}|3\\d{8}|5[03-9]\\d{2,7})","\\d{5,10}",,,"10112345",,,[5,6,7,8,9,10]],,,[,,"NA","NA",,,,,,[-1]]],FJ:[,[,,"[36-9]\\d{6}|0\\d{10}",
"\\d{7}(?:\\d{4})?",,,,,,[7,11]],[,,"(?:3[0-5]|6[25-7]|8[58])\\d{5}","\\d{7}",,,"3212345",,,[7]],[,,"(?:7[0-8]|8[034679]|9\\d)\\d{5}","\\d{7}",,,"7012345",,,[7]],[,,"0800\\d{7}","\\d{11}",,,"08001234567",,,[11]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"FJ",679,"0(?:0|52)",,,,,,"00",,[[,"(\\d{3})(\\d{4})","$1 $2",["[36-9]"]],[,"(\\d{4})(\\d{3})(\\d{4})","$1 $2 $3",["0"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],
1,,[,,"NA","NA",,,,,,[-1]]],FK:[,[,,"[2-7]\\d{4}","\\d{5}",,,,,,[5]],[,,"[2-47]\\d{4}","\\d{5}",,,"31234"],[,,"[56]\\d{4}","\\d{5}",,,"51234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"FK",500,"00",,,,,,,,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],FM:[,[,,"[39]\\d{6}","\\d{7}",,,,,,[7]],[,,"3[2357]0[1-9]\\d{3}|9[2-6]\\d{5}","\\d{7}",,,"3201234"],[,,"3[2357]0[1-9]\\d{3}|9[2-7]\\d{5}",
"\\d{7}",,,"3501234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"FM",691,"00",,,,,,,,[[,"(\\d{3})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],FO:[,[,,"[2-9]\\d{5}","\\d{6}",,,,,,[6]],[,,"(?:20|[3-4]\\d|8[19])\\d{4}","\\d{6}",,,"201234"],[,,"(?:[27][1-9]|5\\d)\\d{4}","\\d{6}",,,"211234"],[,,"80[257-9]\\d{3}","\\d{6}",,,"802123"],[,,"90(?:[1345][15-7]|2[125-7]|99)\\d{2}",
"\\d{6}",,,"901123"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"(?:6[0-36]|88)\\d{4}","\\d{6}",,,"601234"],"FO",298,"00",,,,"(10(?:01|[12]0|88))",,,,[[,"(\\d{6})","$1",,,"$CC $1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],FR:[,[,,"[1-9]\\d{8}","\\d{9}",,,,,,[9]],[,,"[1-5]\\d{8}","\\d{9}",,,"123456789"],[,,"(?:6\\d|7[3-9])\\d{7}","\\d{9}",,,"612345678"],[,,"80[0-5]\\d{6}","\\d{9}",,,"801234567"],[,,"89[1-37-9]\\d{6}","\\d{9}",,,
"891123456"],[,,"8(?:1[019]|2[0156]|84|90)\\d{6}","\\d{9}",,,"810123456"],[,,"NA","NA",,,,,,[-1]],[,,"9\\d{8}","\\d{9}",,,"912345678"],"FR",33,"00","0",,,"0",,,,[[,"([1-79])(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4 $5",["[1-79]"],"0$1"],[,"(1\\d{2})(\\d{3})","$1 $2",["11"],"$1"],[,"(8\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["8"],"0 $1"]],[[,"([1-79])(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4 $5",["[1-79]"],"0$1"],[,"(8\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["8"],"0 $1"]],[,
,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"80[6-9]\\d{6}","\\d{9}",,,"806123456"],,,[,,"NA","NA",,,,,,[-1]]],GA:[,[,,"0?\\d{7}","\\d{7,8}",,,,,,[7,8]],[,,"01\\d{6}","\\d{8}",,,"01441234",,,[8]],[,,"0?[2-7]\\d{6}","\\d{7,8}",,,"06031234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"GA",241,"00",,,,,,,,[[,"(\\d)(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[2-7]"],"0$1"],[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",
["0"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],GB:[,[,,"\\d{7,10}","\\d{4,10}",,,,,,[7,9,10],[4,5,6,8]],[,,"2(?:0[01378]|3[0189]|4[017]|8[0-46-9]|9[012])\\d{7}|1(?:(?:1(?:3[0-48]|[46][0-4]|5[012789]|7[0-49]|8[01349])|21[0-7]|31[0-8]|[459]1\\d|61[0-46-9]))\\d{6}|1(?:2(?:0[024-9]|2[3-9]|3[3-79]|4[1-689]|[58][02-9]|6[0-4789]|7[013-9]|9\\d)|3(?:0\\d|[25][02-9]|3[02-579]|[468][0-46-9]|7[1235679]|9[24578])|4(?:0[03-9]|[28][02-5789]|[37]\\d|4[02-69]|5[0-8]|[69][0-79])|5(?:0[1235-9]|2[024-9]|3[015689]|4[02-9]|5[03-9]|6\\d|7[0-35-9]|8[0-468]|9[0-5789])|6(?:0[034689]|2[0-35689]|[38][013-9]|4[1-467]|5[0-69]|6[13-9]|7[0-8]|9[0124578])|7(?:0[0246-9]|2\\d|3[023678]|4[03-9]|5[0-46-9]|6[013-9]|7[0-35-9]|8[024-9]|9[02-9])|8(?:0[35-9]|2[1-5789]|3[02-578]|4[0-578]|5[124-9]|6[2-69]|7\\d|8[02-9]|9[02569])|9(?:0[02-589]|2[02-689]|3[1-5789]|4[2-9]|5[0-579]|6[234789]|7[0124578]|8\\d|9[2-57]))\\d{6}|1(?:2(?:0(?:46[1-4]|87[2-9])|545[1-79]|76(?:2\\d|3[1-8]|6[1-6])|9(?:7(?:2[0-4]|3[2-5])|8(?:2[2-8]|7[0-4789]|8[345])))|3(?:638[2-5]|647[23]|8(?:47[04-9]|64[015789]))|4(?:044[1-7]|20(?:2[23]|8\\d)|6(?:0(?:30|5[2-57]|6[1-8]|7[2-8])|140)|8(?:052|87[123]))|5(?:24(?:3[2-79]|6\\d)|276\\d|6(?:26[06-9]|686))|6(?:06(?:4\\d|7[4-79])|295[567]|35[34]\\d|47(?:24|61)|59(?:5[08]|6[67]|74)|955[0-4])|7(?:26(?:6[13-9]|7[0-7])|442\\d|50(?:2[0-3]|[3-68]2|76))|8(?:27[56]\\d|37(?:5[2-5]|8[239])|84(?:3[2-58]))|9(?:0(?:0(?:6[1-8]|85)|52\\d)|3583|4(?:66[1-8]|9(?:2[01]|81))|63(?:23|3[1-4])|9561))\\d{3}|176888[234678]\\d{2}|16977[23]\\d{3}",
"\\d{4,10}",,,"1212345678",,,[9,10]],[,,"7(?:[1-4]\\d\\d|5(?:0[0-8]|[13-9]\\d|2[0-35-9])|7(?:0[1-9]|[1-7]\\d|8[02-9]|9[0-689])|8(?:[014-9]\\d|[23][0-8])|9(?:[04-9]\\d|1[02-9]|2[0-35-9]|3[0-689]))\\d{6}","\\d{10}",,,"7400123456",,,[10]],[,,"80(?:0(?:1111|\\d{6,7})|8\\d{7})|500\\d{6}","\\d{7}(?:\\d{2,3})?",,,"8001234567"],[,,"(?:87[123]|9(?:[01]\\d|8[2349]))\\d{7}","\\d{10}",,,"9012345678",,,[10]],[,,"8(?:4(?:5464\\d|[2-5]\\d{7})|70\\d{7})","\\d{7}(?:\\d{3})?",,,"8431234567",,,[7,10]],[,,"70\\d{8}",
"\\d{10}",,,"7012345678",,,[10]],[,,"56\\d{8}","\\d{10}",,,"5612345678",,,[10]],"GB",44,"00","0"," x",,"0",,,,[[,"(\\d{2})(\\d{4})(\\d{4})","$1 $2 $3",["2|5[56]|7(?:0|6[013-9])","2|5[56]|7(?:0|6(?:[013-9]|2[0-35-9]))"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["1(?:1|\\d1)|3|9[018]"],"0$1"],[,"(\\d{5})(\\d{4,5})","$1 $2",["1(?:38|5[23]|69|76|94)","1(?:387|5(?:24|39)|697|768|946)","1(?:3873|5(?:242|39[456])|697[347]|768[347]|9467)"],"0$1"],[,"(1\\d{3})(\\d{5,6})","$1 $2",["1"],"0$1"],[,"(7\\d{3})(\\d{6})",
"$1 $2",["7(?:[1-5789]|62)","7(?:[1-5789]|624)"],"0$1"],[,"(800)(\\d{4})","$1 $2",["800","8001","80011","800111","8001111"],"0$1"],[,"(845)(46)(4\\d)","$1 $2 $3",["845","8454","84546","845464"],"0$1"],[,"(8\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["8(?:4[2-5]|7[0-3])"],"0$1"],[,"(80\\d)(\\d{3})(\\d{4})","$1 $2 $3",["80"],"0$1"],[,"([58]00)(\\d{6})","$1 $2",["[58]00"],"0$1"]],,[,,"76(?:0[012]|2[356]|4[0134]|5[49]|6[0-369]|77|81|9[39])\\d{6}","\\d{10}",,,"7640123456",,,[10]],1,,[,,"NA","NA",,,,,,[-1]],[,
,"(?:3[0347]|55)\\d{8}","\\d{10}",,,"5512345678",,,[10]],,,[,,"NA","NA",,,,,,[-1]]],GD:[,[,,"[4589]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"473(?:2(?:3[0-2]|69)|3(?:2[89]|86)|4(?:[06]8|3[5-9]|4[0-49]|5[5-79]|68|73|90)|63[68]|7(?:58|84)|800|938)\\d{4}","\\d{7}(?:\\d{3})?",,,"4732691234"],[,,"473(?:4(?:0[2-79]|1[04-9]|20|58)|5(?:2[01]|3[3-8])|901)\\d{4}","\\d{7}(?:\\d{3})?",,,"4734031234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],
[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"GD",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"473",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GE:[,[,,"[34578]\\d{8}","\\d{6,9}",,,,,,[9],[6]],[,,"(?:3(?:[256]\\d|4[124-9]|7[0-4])|4(?:1\\d|2[2-7]|3[1-79]|4[2-8]|7[239]|9[1-7]))\\d{6}","\\d{6,9}",,,"322123456"],[,,"5(?:14|5[01578]|68|7[0147-9]|9[0-35-9])\\d{6}","\\d{9}",,,"555123456"],[,,"800\\d{6}",
"\\d{9}",,,"800123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"706\\d{6}","\\d{9}",,,"706123456"],"GE",995,"00","0",,,"0",,,,[[,"(\\d{3})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[348]"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3",["7"],"0$1"],[,"(\\d{3})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["5"],"$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"706\\d{6}","\\d{9}",,,"706123456"],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GF:[,[,,"[56]\\d{8}","\\d{9}",,,,,,[9]],
[,,"594(?:10|2[012457-9]|3[0-57-9]|4[3-9]|5[7-9]|6[0-3]|9[014])\\d{4}","\\d{9}",,,"594101234"],[,,"694(?:[04][0-7]|1[0-5]|3[018]|[29]\\d)\\d{4}","\\d{9}",,,"694201234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"GF",594,"00","0",,,"0",,,,[[,"(\\d{3})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",,"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GG:[,[,,"[135789]\\d{6,9}",
"\\d{6,10}",,,,,,[7,9,10],[6]],[,,"1481\\d{6}","\\d{6,10}",,,"1481456789",,,[10]],[,,"7(?:781|839|911)\\d{6}","\\d{10}",,,"7781123456",,,[10]],[,,"80(?:0(?:1111|\\d{6,7})|8\\d{7})|500\\d{6}","\\d{7}(?:\\d{2,3})?",,,"8001234567"],[,,"(?:87[123]|9(?:[01]\\d|8[0-3]))\\d{7}","\\d{10}",,,"9012345678",,,[10]],[,,"8(?:4(?:5464\\d|[2-5]\\d{7})|70\\d{7})","\\d{7}(?:\\d{3})?",,,"8431234567",,,[7,10]],[,,"70\\d{8}","\\d{10}",,,"7012345678",,,[10]],[,,"56\\d{8}","\\d{10}",,,"5612345678",,,[10]],"GG",44,"00",
"0"," x",,"0",,,,,,[,,"76(?:0[012]|2[356]|4[0134]|5[49]|6[0-369]|77|81|9[39])\\d{6}","\\d{10}",,,"7640123456",,,[10]],,,[,,"NA","NA",,,,,,[-1]],[,,"(?:3[0347]|55)\\d{8}","\\d{10}",,,"5512345678",,,[10]],,,[,,"NA","NA",,,,,,[-1]]],GH:[,[,,"[235]\\d{8}|8\\d{7}","\\d{7,9}",,,,,,[8,9],[7]],[,,"3(?:0[237]\\d|[167](?:2[0-6]|7\\d)|2(?:2[0-5]|7\\d)|3(?:2[0-3]|7\\d)|4(?:2[013-9]|3[01]|7\\d)|5(?:2[0-7]|7\\d)|8(?:2[0-2]|7\\d)|9(?:20|7\\d))\\d{5}","\\d{7,9}",,,"302345678",,,[9]],[,,"(?:2[034678]\\d|5(?:[047]\\d|5[3-6]|6[01]))\\d{6}",
"\\d{9}",,,"231234567",,,[9]],[,,"800\\d{5}","\\d{8}",,,"80012345",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"GH",233,"00","0",,,"0",,,,[[,"(\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["[235]"],"0$1"],[,"(\\d{3})(\\d{5})","$1 $2",["8"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"800\\d{5}","\\d{8}",,,"80012345",,,[8]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GI:[,[,,"[2568]\\d{7}","\\d{8}",,,,,,[8]],[,,"2(?:00\\d|1(?:6[24-7]|9\\d)|2(?:00|2[2457]))\\d{4}",
"\\d{8}",,,"20012345"],[,,"(?:5[46-8]|62)\\d{6}","\\d{8}",,,"57123456"],[,,"80\\d{6}","\\d{8}",,,"80123456"],[,,"8[1-689]\\d{6}","\\d{8}",,,"88123456"],[,,"87\\d{6}","\\d{8}",,,"87123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"GI",350,"00",,,,,,,,[[,"(\\d{3})(\\d{5})","$1 $2",["2"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GL:[,[,,"[1-689]\\d{5}","\\d{6}",,,,,,[6]],[,,"(?:19|3[1-6]|6[14689]|8[14-79]|9\\d)\\d{4}","\\d{6}",,,"321000"],
[,,"[245][2-9]\\d{4}","\\d{6}",,,"221234"],[,,"80\\d{4}","\\d{6}",,,"801234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"3[89]\\d{4}","\\d{6}",,,"381234"],"GL",299,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GM:[,[,,"[2-9]\\d{6}","\\d{7}",,,,,,[7]],[,,"(?:4(?:[23]\\d{2}|4(?:1[024679]|[6-9]\\d))|5(?:54[0-7]|6(?:[67]\\d)|7(?:1[04]|2[035]|3[58]|48))|8\\d{3})\\d{3}",
"\\d{7}",,,"5661234"],[,,"[23679]\\d{6}","\\d{7}",,,"3012345"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"GM",220,"00",,,,,,,,[[,"(\\d{3})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GN:[,[,,"[367]\\d{7,8}","\\d{8,9}",,,,,,[8,9]],[,,"30(?:24|3[12]|4[1-35-7]|5[13]|6[189]|[78]1|9[1478])\\d{4}","\\d{8}",,,"30241234",,,[8]],[,,"6[02356]\\d{7}","\\d{9}",
,,"601123456",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"722\\d{6}","\\d{9}",,,"722123456",,,[9]],"GN",224,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["3"]],[,"(\\d{3})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[67]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GP:[,[,,"[56]\\d{8}","\\d{9}",,,,,,[9]],[,,"590(?:0[13468]|1[012]|2[0-68]|3[28]|4[0-8]|5[579]|6[0189]|70|8[0-689]|9\\d)\\d{4}",
"\\d{9}",,,"590201234"],[,,"690(?:0[0-7]|[1-9]\\d)\\d{4}","\\d{9}",,,"690301234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"GP",590,"00","0",,,"0",,,,[[,"([56]90)(\\d{2})(\\d{4})","$1 $2-$3",,"0$1"]],,[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GQ:[,[,,"[23589]\\d{8}","\\d{9}",,,,,,[9]],[,,"3(?:3(?:3\\d[7-9]|[0-24-9]\\d[46])|5\\d{2}[7-9])\\d{4}","\\d{9}",,,"333091234"],
[,,"(?:222|55[15])\\d{6}","\\d{9}",,,"222123456"],[,,"80\\d[1-9]\\d{5}","\\d{9}",,,"800123456"],[,,"90\\d[1-9]\\d{5}","\\d{9}",,,"900123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"GQ",240,"00",,,,,,,,[[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3",["[235]"]],[,"(\\d{3})(\\d{6})","$1 $2",["[89]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GR:[,[,,"[26-9]\\d{9}","\\d{10}",,,,,,[10]],[,,"2(?:1\\d{2}|2(?:2[1-46-9]|3[1-8]|4[1-7]|5[1-4]|6[1-8]|7[1-5]|[89][1-9])|3(?:1\\d|2[1-57]|[35][1-3]|4[13]|7[1-7]|8[124-6]|9[1-79])|4(?:1\\d|2[1-8]|3[1-4]|4[13-5]|6[1-578]|9[1-5])|5(?:1\\d|[29][1-4]|3[1-5]|4[124]|5[1-6])|6(?:1\\d|3[1245]|4[1-7]|5[13-9]|[269][1-6]|7[14]|8[1-5])|7(?:1\\d|2[1-5]|3[1-6]|4[1-7]|5[1-57]|6[135]|9[125-7])|8(?:1\\d|2[1-5]|[34][1-4]|9[1-57]))\\d{6}",
"\\d{10}",,,"2123456789"],[,,"69\\d{8}","\\d{10}",,,"6912345678"],[,,"800\\d{7}","\\d{10}",,,"8001234567"],[,,"90[19]\\d{7}","\\d{10}",,,"9091234567"],[,,"8(?:0[16]|12|25)\\d{7}","\\d{10}",,,"8011234567"],[,,"70\\d{8}","\\d{10}",,,"7012345678"],[,,"NA","NA",,,,,,[-1]],"GR",30,"00",,,,,,,,[[,"([27]\\d)(\\d{4})(\\d{4})","$1 $2 $3",["21|7"]],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["2[2-9]1|[689]"]],[,"(2\\d{3})(\\d{6})","$1 $2",["2[2-9][02-9]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,
,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GT:[,[,,"[2-7]\\d{7}|1[89]\\d{9}","\\d{8}(?:\\d{3})?",,,,,,[8,11]],[,,"[267][2-9]\\d{6}","\\d{8}",,,"22456789",,,[8]],[,,"[345]\\d{7}","\\d{8}",,,"51234567",,,[8]],[,,"18[01]\\d{8}","\\d{11}",,,"18001112222",,,[11]],[,,"19\\d{9}","\\d{11}",,,"19001112222",,,[11]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"GT",502,"00",,,,,,,,[[,"(\\d{4})(\\d{4})","$1 $2",["[2-7]"]],[,"(\\d{4})(\\d{3})(\\d{4})","$1 $2 $3",["1"]]],,[,,"NA",
"NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GU:[,[,,"[5689]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"671(?:3(?:00|3[39]|4[349]|55|6[26])|4(?:56|7[1-9]|8[236-9])|5(?:55|6[2-5]|88)|6(?:3[2-578]|4[24-9]|5[34]|78|8[5-9])|7(?:[079]7|2[0167]|3[45]|47|8[789])|8(?:[2-5789]8|6[48])|9(?:2[29]|6[79]|7[179]|8[789]|9[78]))\\d{4}","\\d{7}(?:\\d{3})?",,,"6713001234"],[,,"671(?:3(?:00|3[39]|4[349]|55|6[26])|4(?:56|7[1-9]|8[236-9])|5(?:55|6[2-5]|88)|6(?:3[2-578]|4[24-9]|5[34]|78|8[5-9])|7(?:[079]7|2[0167]|3[45]|47|8[789])|8(?:[2-5789]8|6[48])|9(?:2[29]|6[79]|7[179]|8[789]|9[78]))\\d{4}",
"\\d{7}(?:\\d{3})?",,,"6713001234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"GU",1,"011","1",,,"1",,,1,,,[,,"NA","NA",,,,,,[-1]],,"671",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GW:[,[,,"(?:4(?:0\\d{5}|4\\d{7})|9\\d{8})","\\d{7,9}",,,,,,[7,9]],[,,"443(?:2[0125]|3[1245]|4[12]|5[1-4]|70|9[1-467])\\d{4}",
"\\d{9}",,,"443201234",,,[9]],[,,"9(?:55\\d|6(?:6\\d|9[012])|77\\d)\\d{5}","\\d{9}",,,"955012345",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"40\\d{5}","\\d{7}",,,"4012345",,,[7]],"GW",245,"00",,,,,,,,[[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3",["44|9[567]"]],[,"(\\d{3})(\\d{4})","$1 $2",["40"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],GY:[,[,,"[2-4679]\\d{6}","\\d{7}",,,,,,[7]],
[,,"(?:2(?:1[6-9]|2[0-35-9]|3[1-4]|5[3-9]|6\\d|7[0-24-79])|3(?:2[25-9]|3\\d)|4(?:4[0-24]|5[56])|77[1-57])\\d{4}","\\d{7}",,,"2201234"],[,,"6\\d{6}","\\d{7}",,,"6091234"],[,,"(?:289|862)\\d{4}","\\d{7}",,,"2891234"],[,,"9008\\d{3}","\\d{7}",,,"9008123"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"GY",592,"001",,,,,,,,[[,"(\\d{3})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],HK:[,[,,"[235-7]\\d{7}|8\\d{7,8}|9\\d{4,10}",
"\\d{5,11}",,,,,,[5,6,7,8,9,11]],[,,"(?:[23]\\d|58)\\d{6}","\\d{8}",,,"21234567",,,[8]],[,,"(?:5[1-79]\\d|6\\d{2}|8[4-79]\\d|9(?:0[1-9]|[1-8]\\d))\\d{5}","\\d{8}",,,"51234567",,,[8]],[,,"800\\d{6}","\\d{9}",,,"800123456",,,[9]],[,,"900(?:[0-24-9]\\d{7}|3\\d{1,4})","\\d{5,11}",,,"90012345678",,,[5,6,7,8,11]],[,,"NA","NA",,,,,,[-1]],[,,"8[1-3]\\d{6}","\\d{8}",,,"81123456",,,[8]],[,,"NA","NA",,,,,,[-1]],"HK",852,"00(?:[126-9]|30|5[09])?",,,,,,"00",,[[,"(\\d{4})(\\d{4})","$1 $2",["[235-7]|[89](?:0[1-9]|[1-9])"]],
[,"(800)(\\d{3})(\\d{3})","$1 $2 $3",["800"]],[,"(900)(\\d{2})(\\d{3})(\\d{3})","$1 $2 $3 $4",["900"]],[,"(900)(\\d{2,5})","$1 $2",["900"]]],,[,,"7\\d{7}","\\d{8}",,,"71234567",,,[8]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],HN:[,[,,"[237-9]\\d{7}","\\d{8}",,,,,,[8]],[,,"2(?:2(?:0[019]|1[1-36]|[23]\\d|4[056]|5[57]|7[01389]|8[0146-9]|9[012])|4(?:07|2[3-59]|3[13-689]|4[0-68]|5[1-35])|5(?:16|4[3-5]|5\\d|6[4-6]|74)|6(?:[056]\\d|17|3[04]|4[0-378]|[78][0-8]|9[01])|7(?:6[46-9]|7[02-9]|8[034])|8(?:79|8[0-35789]|9[1-57-9]))\\d{4}",
"\\d{8}",,,"22123456"],[,,"[37-9]\\d{7}","\\d{8}",,,"91234567"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"HN",504,"00",,,,,,,,[[,"(\\d{4})(\\d{4})","$1-$2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],HR:[,[,,"[1-7]\\d{5,8}|[89]\\d{6,11}","\\d{6,12}",,,,,,[6,7,8,9,10,11,12]],[,,"1\\d{7}|(?:2[0-3]|3[1-5]|4[02-47-9]|5[1-3])\\d{6,7}","\\d{6,9}",,,"12345678",,,[8,9]],
[,,"9(?:[1-9]\\d{6,10}|01\\d{6,9})","\\d{8,12}",,,"912345678",,,[8,9,10,11,12]],[,,"80[01]\\d{4,7}","\\d{7,10}",,,"8001234567",,,[7,8,9,10]],[,,"6(?:[01459]\\d{4,7})","\\d{6,9}",,,"611234",,,[6,7,8,9]],[,,"NA","NA",,,,,,[-1]],[,,"7[45]\\d{4,7}","\\d{6,9}",,,"741234567",,,[6,7,8,9]],[,,"NA","NA",,,,,,[-1]],"HR",385,"00","0",,,"0",,,,[[,"(1)(\\d{4})(\\d{3})","$1 $2 $3",["1"],"0$1"],[,"(6[09])(\\d{4})(\\d{3})","$1 $2 $3",["6[09]"],"0$1"],[,"([67]2)(\\d{3})(\\d{3,4})","$1 $2 $3",["[67]2"],"0$1"],[,"([2-5]\\d)(\\d{3})(\\d{3,4})",
"$1 $2 $3",["[2-5]"],"0$1"],[,"(9\\d)(\\d{3})(\\d{3,4})","$1 $2 $3",["9"],"0$1"],[,"(9\\d)(\\d{4})(\\d{4})","$1 $2 $3",["9"],"0$1"],[,"(9\\d)(\\d{3,4})(\\d{3})(\\d{3})","$1 $2 $3 $4",["9"],"0$1"],[,"(\\d{2})(\\d{2})(\\d{2,3})","$1 $2 $3",["6[0145]|7"],"0$1"],[,"(\\d{2})(\\d{3,4})(\\d{3})","$1 $2 $3",["6[0145]|7"],"0$1"],[,"(80[01])(\\d{2})(\\d{2,3})","$1 $2 $3",["8"],"0$1"],[,"(80[01])(\\d{3,4})(\\d{3})","$1 $2 $3",["8"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"[76]2\\d{6,7}",
"\\d{8,9}",,,"62123456",,,[8,9]],,,[,,"NA","NA",,,,,,[-1]]],HT:[,[,,"[2-489]\\d{7}","\\d{8}",,,,,,[8]],[,,"2(?:[248]\\d|5[1-5]|94)\\d{5}","\\d{8}",,,"22453300"],[,,"(?:3[1-9]\\d|4\\d{2}|9(?:8[0-35]|9[5-9]))\\d{5}","\\d{8}",,,"34101234"],[,,"8\\d{7}","\\d{8}",,,"80012345"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"98[89]\\d{5}","\\d{8}",,,"98901234"],"HT",509,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{4})","$1 $2 $3"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],
[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],HU:[,[,,"[1-9]\\d{7,8}","\\d{6,9}",,,,,,[8,9],[6]],[,,"(?:1\\d|2[2-9]|3[2-7]|4[24-9]|5[2-79]|6[23689]|7[2-9]|8[2-57-9]|9[2-69])\\d{6}","\\d{6,8}",,,"12345678",,,[8]],[,,"(?:[257]0|3[01])\\d{7}","\\d{9}",,,"201234567",,,[9]],[,,"[48]0\\d{6}","\\d{8}",,,"80123456",,,[8]],[,,"9[01]\\d{6}","\\d{8}",,,"90123456",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"21\\d{7}","\\d{9}",,,"211234567",,,[9]],"HU",36,"00","06",,,"06",,,,[[,"(1)(\\d{3})(\\d{4})",
"$1 $2 $3",["1"],"($1)"],[,"(\\d{2})(\\d{3})(\\d{3,4})","$1 $2 $3",["[2-9]"],"($1)"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"[48]0\\d{6}","\\d{8}",,,"80123456",,,[8]],[,,"38\\d{7}","\\d{6,9}",,,"381234567",,,[9]],,,[,,"NA","NA",,,,,,[-1]]],ID:[,[,,"(?:[1-79]\\d{6,10}|8\\d{7,11})","\\d{5,12}",,,,,,[7,8,9,10,11,12],[5,6]],[,,"2(?:1(?:14\\d{3}|[0-8]\\d{6,7}|500\\d{3}|9\\d{6})|2\\d{6,8}|4\\d{7,8})|(?:2(?:[35][1-4]|6[0-8]|7[1-6]|8\\d|9[1-8])|3(?:1|[25][1-8]|3[1-68]|4[1-3]|6[1-3568]|7[0-469]|8\\d)|4(?:0[1-589]|1[01347-9]|2[0-36-8]|3[0-24-68]|43|5[1-378]|6[1-5]|7[134]|8[1245])|5(?:1[1-35-9]|2[25-8]|3[124-9]|4[1-3589]|5[1-46]|6[1-8])|6(?:19?|[25]\\d|3[1-69]|4[1-6])|7(?:02|[125][1-9]|[36]\\d|4[1-8]|7[0-36-9])|9(?:0[12]|1[013-8]|2[0-479]|5[125-8]|6[23679]|7[159]|8[01346]))\\d{5,8}",
"\\d{5,11}",,,"612345678",,,[7,8,9,10,11]],[,,"(?:2(?:1(?:3[145]|4[01]|5[1-469]|60|8[0359]|9\\d)|2(?:88|9[1256])|3[1-4]9|4(?:36|91)|5(?:1[349]|[2-4]9)|6[0-7]9|7(?:[1-36]9|4[39])|8[1-5]9|9[1-48]9)|3(?:19[1-3]|2[12]9|3[13]9|4(?:1[69]|39)|5[14]9|6(?:1[69]|2[89])|709)|4[13]19|5(?:1(?:19|8[39])|4[129]9|6[12]9)|6(?:19[12]|2(?:[23]9|77))|7(?:1[13]9|2[15]9|419|5(?:1[89]|29)|6[15]9|7[178]9))\\d{5,6}|8[1-35-9]\\d{7,10}","\\d{9,12}",,,"812345678",,,[9,10,11,12]],[,,"177\\d{6,8}|800\\d{5,7}","\\d{8,11}",,,"8001234567",
,,[8,9,10,11]],[,,"809\\d{7}","\\d{10}",,,"8091234567",,,[10]],[,,"804\\d{7}","\\d{10}",,,"8041234567",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"ID",62,"0(?:0[1789]|10(?:00|1[67]))","0",,,"0",,,,[[,"(\\d{2})(\\d{5,8})","$1 $2",["2[124]|[36]1"],"(0$1)"],[,"(\\d{3})(\\d{5,8})","$1 $2",["[4579]|2[035-9]|[36][02-9]"],"(0$1)"],[,"(8\\d{2})(\\d{3,4})(\\d{3,5})","$1-$2-$3",["8[1-35-9]"],"0$1"],[,"(1)(500)(\\d{3})","$1 $2 $3",["15"],"$1"],[,"(177)(\\d{6,8})","$1 $2",["17"],"0$1"],[,"(800)(\\d{5,7})",
"$1 $2",["800"],"0$1"],[,"(804)(\\d{3})(\\d{4})","$1 $2 $3",["804"],"0$1"],[,"(80\\d)(\\d)(\\d{3})(\\d{3})","$1 $2 $3 $4",["80[79]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"8071\\d{6}","\\d{10}",,,"8071123456",,,[10]],[,,"1500\\d{3}|8071\\d{6}","\\d{7,10}",,,"8071123456",,,[7,10]],,,[,,"NA","NA",,,,,,[-1]]],IE:[,[,,"[124-9]\\d{6,9}","\\d{5,10}",,,,,,[7,8,9,10],[5,6]],[,,"1\\d{7,8}|2(?:1\\d{6,7}|3\\d{7}|[24-9]\\d{5})|4(?:0[24]\\d{5}|[1-469]\\d{7}|5\\d{6}|7\\d{5}|8[0-46-9]\\d{7})|5(?:0[45]\\d{5}|1\\d{6}|[23679]\\d{7}|8\\d{5})|6(?:1\\d{6}|[237-9]\\d{5}|[4-6]\\d{7})|7[14]\\d{7}|9(?:1\\d{6}|[04]\\d{7}|[35-9]\\d{5})",
"\\d{5,10}",,,"2212345"],[,,"8(?:22\\d{6}|[35-9]\\d{7})","\\d{9}",,,"850123456",,,[9]],[,,"1800\\d{6}","\\d{10}",,,"1800123456",,,[10]],[,,"15(?:1[2-8]|[2-8]0|9[089])\\d{6}","\\d{10}",,,"1520123456",,,[10]],[,,"18[59]0\\d{6}","\\d{10}",,,"1850123456",,,[10]],[,,"700\\d{6}","\\d{9}",,,"700123456",,,[9]],[,,"76\\d{7}","\\d{9}",,,"761234567",,,[9]],"IE",353,"00","0",,,"0",,,,[[,"(1)(\\d{3,4})(\\d{4})","$1 $2 $3",["1"],"(0$1)"],[,"(\\d{2})(\\d{5})","$1 $2",["2[24-9]|47|58|6[237-9]|9[35-9]"],"(0$1)"],
[,"(\\d{3})(\\d{5})","$1 $2",["40[24]|50[45]"],"(0$1)"],[,"(48)(\\d{4})(\\d{4})","$1 $2 $3",["48"],"(0$1)"],[,"(818)(\\d{3})(\\d{3})","$1 $2 $3",["81"],"(0$1)"],[,"(\\d{2})(\\d{3})(\\d{3,4})","$1 $2 $3",["[24-69]|7[14]"],"(0$1)"],[,"([78]\\d)(\\d{3,4})(\\d{4})","$1 $2 $3",["76|8[35-9]"],"0$1"],[,"(700)(\\d{3})(\\d{3})","$1 $2 $3",["70"],"0$1"],[,"(\\d{4})(\\d{3})(\\d{3})","$1 $2 $3",["1(?:8[059]|5)","1(?:8[059]0|5)"],"$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"18[59]0\\d{6}","\\d{10}",,,"1850123456",,,
[10]],[,,"818\\d{6}","\\d{9}",,,"818123456",,,[9]],,,[,,"8[35-9]\\d{8}","\\d{10}",,,"8501234567",,,[10]]],IL:[,[,,"[17]\\d{6,9}|[2-589]\\d{3}(?:\\d{3,6})?|6\\d{3}","\\d{4,10}",,,,,,[4,7,8,9,10]],[,,"[2-489]\\d{7}","\\d{7,8}",,,"21234567",,,[8]],[,,"5(?:[02-47-9]\\d{2}|5(?:01|2[23]|3[2-4]|4[45]|5[5689]|6[67]|7[0178]|8[6-9]|9[4-9])|6[2-9]\\d)\\d{5}","\\d{9}",,,"501234567",,,[9]],[,,"1(?:80[019]\\d{3}|255)\\d{3}","\\d{7,10}",,,"1800123456",,,[7,10]],[,,"1(?:212|(?:9(?:0[01]|19)|200)\\d{2})\\d{4}","\\d{8,10}",
,,"1919123456",,,[8,9,10]],[,,"1700\\d{6}","\\d{10}",,,"1700123456",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"7(?:18\\d|2[23]\\d|3[237]\\d|47\\d|6(?:5\\d|8[0168])|7\\d{2}|8(?:2\\d|33|55|77|81)|9[29]\\d)\\d{5}","\\d{9}",,,"771234567",,,[9]],"IL",972,"0(?:0|1[2-9])","0",,,"0",,,,[[,"([2-489])(\\d{3})(\\d{4})","$1-$2-$3",["[2-489]"],"0$1"],[,"([57]\\d)(\\d{3})(\\d{4})","$1-$2-$3",["[57]"],"0$1"],[,"(1)([7-9]\\d{2})(\\d{3})(\\d{3})","$1-$2-$3-$4",["1[7-9]"],"$1"],[,"(1255)(\\d{3})","$1-$2",["125"],"$1"],[,
"(1200)(\\d{3})(\\d{3})","$1-$2-$3",["120"],"$1"],[,"(1212)(\\d{2})(\\d{2})","$1-$2-$3",["121"],"$1"],[,"(1599)(\\d{6})","$1-$2",["15"],"$1"],[,"(\\d{4})","*$1",["[2-689]"],"$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"1700\\d{6}|[2-689]\\d{3}","\\d{4,10}",,,"1700123456",,,[4,10]],[,,"[2-689]\\d{3}|1599\\d{6}","\\d{4}(?:\\d{6})?",,,"1599123456",,,[4,10]],,,[,,"NA","NA",,,,,,[-1]]],IM:[,[,,"[135789]\\d{6,9}","\\d{6,10}",,,,,,[10],[6]],[,,"1624\\d{6}","\\d{6,10}",,,"1624456789"],[,,"7[569]24\\d{6}","\\d{10}",
,,"7924123456"],[,,"808162\\d{4}","\\d{10}",,,"8081624567"],[,,"(?:872299|90[0167]624)\\d{4}","\\d{10}",,,"9016247890"],[,,"8(?:4(?:40[49]06|5624\\d)|70624\\d)\\d{3}","\\d{10}",,,"8456247890"],[,,"70\\d{8}","\\d{10}",,,"7012345678"],[,,"56\\d{8}","\\d{10}",,,"5612345678"],"IM",44,"00","0"," x",,"0",,,,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"3(?:08162\\d|3\\d{5}|4(?:40[49]06|5624\\d)|7(?:0624\\d|2299\\d))\\d{3}|55\\d{8}","\\d{10}",,,"5512345678"],,,[,,"NA","NA",,,,,,[-1]]],IN:[,[,,
"008\\d{9}|1\\d{7,12}|[2-9]\\d{9,10}","\\d{6,13}",,,,,,[8,9,10,11,12,13],[6,7]],[,,"(?:11|2[02]|33|4[04]|79)[2-7]\\d{7}|80[2-467]\\d{7}|(?:1(?:2[0-249]|3[0-25]|4[145]|[59][14]|6[014]|7[1257]|8[01346])|2(?:1[257]|3[013]|4[01]|5[0137]|6[0158]|78|8[1568]|9[14])|3(?:26|4[1-3]|5[34]|6[01489]|7[02-46]|8[159])|4(?:1[36]|2[1-47]|3[15]|5[12]|6[0-26-9]|7[0-24-9]|8[013-57]|9[014-7])|5(?:1[025]|[36][25]|22|4[28]|5[12]|[78]1|9[15])|6(?:12|[2345]1|57|6[13]|7[14]|80)|7(?:12|2[14]|3[134]|4[47]|5[15]|[67]1|88)|8(?:16|2[014]|3[126]|6[136]|7[078]|8[34]|91))[2-7]\\d{6}|(?:(?:1(?:2[35-8]|3[346-9]|4[236-9]|[59][0235-9]|6[235-9]|7[34689]|8[257-9])|2(?:1[134689]|3[24-8]|4[2-8]|5[25689]|6[2-4679]|7[13-79]|8[2-479]|9[235-9])|3(?:01|1[79]|2[1-5]|4[25-8]|5[125689]|6[235-7]|7[157-9]|8[2-467])|4(?:1[14578]|2[5689]|3[2-467]|5[4-7]|6[35]|73|8[2689]|9[2389])|5(?:[16][146-9]|2[14-8]|3[1346]|4[14-69]|5[46]|7[2-4]|8[2-8]|9[246])|6(?:1[1358]|2[2457]|3[2-4]|4[235-7]|[57][2-689]|6[24-578]|8[1-6])|8(?:1[1357-9]|2[235-8]|3[03-57-9]|4[0-24-9]|5\\d|6[2457-9]|7[1-6]|8[1256]|9[2-4]))\\d|7(?:(?:1[013-9]|2[0235-9]|3[2679]|4[1-35689]|5[2-46-9]|[67][02-9]|9\\d)\\d|8(?:2[0-6]|[013-8]\\d)))[2-7]\\d{5}",
"\\d{6,10}",,,"1123456789",,,[10]],[,,"(?:7(?:0\\d{3}|2(?:[0235679]\\d{2}|[14][017-9]\\d|8(?:[0-59]\\d|6[089]|78)|9[389]\\d)|3(?:[05-8]\\d{2}|1(?:[089]\\d|7[5-8])|2(?:[5-8]\\d|[01][089])|3(?:07|[17-9]\\d)|4(?:[07-9]\\d|11)|9(?:[01689]\\d|59|70))|4(?:0[1-9]\\d|1(?:[015-9]\\d|2[089]|4[08])|2(?:09|[1-7][089]|[89]\\d)|3(?:[0-8][089]|9\\d)|4(?:[089]\\d|11|7[02-8])|5(?:0[089]|[59]9)|7(?:0[3-9]|11|7[02-8]|[89]\\d)|8(?:[0-24-7][089]|[389]\\d)|9(?:[0-6][089]|7[08]|[89]\\d))|5(?:[034678]\\d|2[03-9]|5[017-9]|9[7-9])\\d|6(?:0[0-47]|1[0-257-9]|2[0-4]|3[19]|5[4589]|[6-9]\\d)\\d|7(?:0[2-9]|[1-79]\\d|8[1-9])\\d|8(?:[0-79]\\d{2}|88[01])|99[4-9]\\d)|8(?:0(?:[01589]\\d|6[67])|1(?:[02-57-9]\\d|1[0135-9])|2(?:[236-9]\\d|5[1-9])|3(?:[0357-9]\\d|4[1-9])|[45]\\d{2}|6[02457-9]\\d|7(?:07|[1-69]\\d)|8(?:[0-26-9]\\d|44|5[2-9])|9(?:[035-9]\\d|2[2-9]|4[0-8]))\\d|9\\d{4})\\d{5}",
"\\d{10}",,,"9987654321",,,[10]],[,,"00800\\d{7}|1(?:600\\d{6}|80(?:0\\d{4,9}|3\\d{9}))","\\d{8,13}",,,"1800123456"],[,,"186[12]\\d{9}","\\d{13}",,,"1861123456789",,,[13]],[,,"1860\\d{7}","\\d{11}",,,"18603451234",,,[11]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"IN",91,"00","0",,,"0",,,,[[,"(\\d{5})(\\d{5})","$1 $2",["7(?:[023578]|4[0-57-9]|6[0-35-9]|99)|8(?:0[015689]|1[0-57-9]|2[2356-9]|3[0-57-9]|[45]|6[02457-9]|7[01-69]|8[0-24-9]|9[02-9])|9","7(?:[08]|2(?:[0235679]|[14][017-9]|8[0-79]|9[389])|3(?:[05-8]|1[07-9]|2[015-8]|[34][017-9]|9[015-9])|4(?:0[1-9]|1[0-24-9]|[2389]|[47][017-9]|5[059])|5(?:[034678]|2[03-9]|5[017-9]|9[7-9])|6(?:0[0-47]|1[0-257-9]|2[0-4]|3[19]|5[4589]|[6-9])|7(?:0[2-9]|[1-79]|8[1-9])|99[4-9])|8(?:0(?:[01589]|6[67])|1(?:[02-57-9]|1[0135-9])|2(?:[236-9]|5[1-9])|3(?:[0357-9]|4[1-9])|[45]|6[02457-9]|7(?:07|[1-69])|8(?:[0-26-9]|44|5[2-9])|9(?:[035-9]|2[2-9]|4[0-8]))|9",
"7(?:0|2(?:[0235679]|[14][017-9]|8(?:[0-569]|78)|9[389])|3(?:[05-8]|1(?:[089]|7[5-9])|2(?:[5-8]|[01][089])|3[017-9]|4(?:[07-9]|11)|9(?:[01689]|59|70))|4(?:0[1-9]|1(?:[015-9]|2[089]|4[08])|2(?:09|[1-7][089]|[89])|3(?:[0-8][089]|9)|4(?:[089]|11|7[02-8])|5(?:0[089]|[59]9)|7(?:0[3-9]|11|7[02-8]|[89])|8(?:[0-24-7][089]|[389])|9(?:[0-6][089]|7[08]|[89]))|5(?:[034678]|2[03-9]|5[017-9]|9[7-9])|6(?:0[0-47]|1[0-257-9]|2[0-4]|3[19]|5[4589]|[6-9])|7(?:0[2-9]|[1-79]|8[1-9])|8(?:[0-79]|88[01])|99[4-9])|8(?:0(?:[01589]|6[67])|1(?:[02-57-9]|1[0135-9])|2(?:[236-9]|5[1-9])|3(?:[0357-9]|4[1-9])|[45]|6[02457-9]|7(?:07|[1-69])|8(?:[0-26-9]|44|5[2-9])|9(?:[035-9]|2[2-9]|4[0-8]))|9"],
"0$1",,1],[,"(\\d{2})(\\d{4})(\\d{4})","$1 $2 $3",["11|2[02]|33|4[04]|79|80[2-46]"],"0$1",,1],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["1(?:2[0-249]|3[0-25]|4[145]|[569][14]|7[1257]|8[1346]|[68][1-9])|2(?:1[257]|3[013]|4[01]|5[0137]|6[0158]|78|8[1568]|9[14])|3(?:26|4[1-3]|5[34]|6[01489]|7[02-46]|8[159])|4(?:1[36]|2[1-47]|3[15]|5[12]|6[0-26-9]|7[0-24-9]|8[013-57]|9[014-7])|5(?:1[025]|[36][25]|22|4[28]|5[12]|[78]1|9[15])|6(?:12|[2345]1|57|6[13]|7[14]|80)"],"0$1",,1],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",
["7(?:12|2[14]|3[134]|4[47]|5[15]|[67]1|88)","7(?:12|2[14]|3[134]|4[47]|5(?:1|5[2-6])|[67]1|88)"],"0$1",,1],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["8(?:16|2[014]|3[126]|6[136]|7[078]|8[34]|91)"],"0$1",,1],[,"(\\d{4})(\\d{3})(\\d{3})","$1 $2 $3",["1(?:[23579]|[468][1-9])|[2-8]"],"0$1",,1],[,"(\\d{2})(\\d{3})(\\d{4})(\\d{3})","$1 $2 $3 $4",["008"],"0$1",,1],[,"(1600)(\\d{2})(\\d{4})","$1 $2 $3",["160","1600"],"$1",,1],[,"(1800)(\\d{4,5})","$1 $2",["180","1800"],"$1",,1],[,"(18[06]0)(\\d{2,4})(\\d{4})",
"$1 $2 $3",["18[06]","18[06]0"],"$1",,1],[,"(140)(\\d{3})(\\d{4})","$1 $2 $3",["140"],"$1",,1],[,"(\\d{4})(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3 $4",["18[06]","18(?:0[03]|6[12])"],"$1",,1]],,[,,"NA","NA",,,,,,[-1]],,,[,,"00800\\d{7}|1(?:600\\d{6}|8(?:0(?:0\\d{4,9}|3\\d{9})|6(?:0\\d{7}|[12]\\d{9})))","\\d{8,13}",,,"1800123456"],[,,"140\\d{7}","\\d{10}",,,"1409305260",,,[10]],1,,[,,"NA","NA",,,,,,[-1]]],IO:[,[,,"3\\d{6}","\\d{7}",,,,,,[7]],[,,"37\\d{5}","\\d{7}",,,"3709100"],[,,"38\\d{5}","\\d{7}",,,"3801234"],
[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"IO",246,"00",,,,,,,,[[,"(\\d{3})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],IQ:[,[,,"[1-7]\\d{7,9}","\\d{6,10}",,,,,,[8,9,10],[6,7]],[,,"1\\d{7}|(?:2[13-5]|3[02367]|4[023]|5[03]|6[026])\\d{6,7}","\\d{6,9}",,,"12345678",,,[8,9]],[,,"7[3-9]\\d{8}","\\d{10}",,,"7912345678",,,[10]],[,,"NA","NA",,,,,,[-1]],
[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"IQ",964,"00","0",,,"0",,,,[[,"(1)(\\d{3})(\\d{4})","$1 $2 $3",["1"],"0$1"],[,"([2-6]\\d)(\\d{3})(\\d{3,4})","$1 $2 $3",["[2-6]"],"0$1"],[,"(7\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["7"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],IR:[,[,,"[1-8]\\d{9}|9(?:[0-4]\\d{8}|9\\d{2,8})","\\d{4,10}",,,,,,[4,5,6,7,8,9,10]],[,,"(?:1[137]|2[13-68]|3[1458]|4[145]|5[146-8]|6[146]|7[1467]|8[13467])\\d{8}",
"\\d{10}",,,"2123456789",,,[10]],[,,"9(?:0[1-3]|[13]\\d|2[0-2]|90)\\d{7}","\\d{10}",,,"9123456789",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"(?:[2-6]0\\d|993)\\d{7}","\\d{10}",,,"9932123456",,,[10]],"IR",98,"00","0",,,"0",,,,[[,"(21)(\\d{3,5})","$1 $2",["21"],"0$1"],[,"(\\d{2})(\\d{4})(\\d{4})","$1 $2 $3",["[1-8]"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{3,4})","$1 $2 $3",["9"],"0$1"],[,"(\\d{3})(\\d{2})(\\d{2,3})","$1 $2 $3",["9"],"0$1"],[,
"(\\d{3})(\\d{3})","$1 $2",["9"],"0$1"]],,[,,"943\\d{7}","\\d{10}",,,"9432123456",,,[10]],,,[,,"NA","NA",,,,,,[-1]],[,,"9990\\d{0,6}","\\d{4,10}",,,"9990123456"],,,[,,"NA","NA",,,,,,[-1]]],IS:[,[,,"[4-9]\\d{6}|38\\d{7}","\\d{7,9}",,,,,,[7,9]],[,,"(?:4(?:1[0-24-6]|2[0-7]|[37][0-8]|4[0-245]|5[0-68]|6\\d|8[0-36-8])|5(?:05|[156]\\d|2[02578]|3[013-79]|4[03-7]|7[0-2578]|8[0-35-9]|9[013-689])|87[23])\\d{4}","\\d{7}",,,"4101234",,,[7]],[,,"38[589]\\d{6}|(?:6(?:1[1-8]|2[056]|3[089]|4[0167]|5[0159]|[67][0-69]|9\\d)|7(?:5[057]|6[0-2]|[78]\\d)|8(?:2[0-59]|3[0-4]|[469]\\d|5[1-9]))\\d{4}",
"\\d{7,9}",,,"6111234"],[,,"800\\d{4}","\\d{7}",,,"8001234",,,[7]],[,,"90\\d{5}","\\d{7}",,,"9011234",,,[7]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"49\\d{5}","\\d{7}",,,"4921234",,,[7]],"IS",354,"1(?:0(?:01|10|20)|100)|00",,,,,,"00",,[[,"(\\d{3})(\\d{4})","$1 $2",["[4-9]"]],[,"(3\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["3"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"809\\d{4}","\\d{7}",,,"8091234",,,[7]],,,[,,"(?:6(?:2[1-478]|49|8\\d)|8(?:7[0189]|80)|95[48])\\d{4}","\\d{7}",
,,"6211234",,,[7]]],IT:[,[,,"[01589]\\d{5,10}|3(?:[12457-9]\\d{8}|[36]\\d{7,9})","\\d{6,11}",,,,,,[6,7,8,9,10,11]],[,,"0(?:[26]\\d{4,9}|(?:1(?:[0159]\\d|[27][1-5]|31|4[1-4]|6[1356]|8[2-57])|3(?:[0159]\\d|2[1-4]|3[12]|[48][1-6]|6[2-59]|7[1-7])|4(?:[0159]\\d|[23][1-9]|4[245]|6[1-5]|7[1-4]|81)|5(?:[0159]\\d|2[1-5]|3[2-6]|4[1-79]|6[4-6]|7[1-578]|8[3-8])|7(?:[0159]\\d|2[12]|3[1-7]|4[2346]|6[13569]|7[13-6]|8[1-59])|8(?:[0159]\\d|2[34578]|3[1-356]|[6-8][1-5])|9(?:[0159]\\d|[238][1-5]|4[12]|6[1-8]|7[1-6]))\\d{2,7})",
"\\d{6,11}",,,"0212345678"],[,,"3(?:[12457-9]\\d{8}|6\\d{7,8}|3\\d{7,9})","\\d{9,11}",,,"3123456789",,,[9,10,11]],[,,"80(?:0\\d{6}|3\\d{3})","\\d{6,9}",,,"800123456",,,[6,9]],[,,"0878\\d{5}|1(?:44|6[346])\\d{6}|89(?:2\\d{3}|4(?:[0-4]\\d{2}|[5-9]\\d{4})|5(?:[0-4]\\d{2}|[5-9]\\d{6})|9\\d{6})","\\d{6,10}",,,"899123456",,,[6,8,9,10]],[,,"84(?:[08]\\d{6}|[17]\\d{3})","\\d{6,9}",,,"848123456",,,[6,9]],[,,"1(?:78\\d|99)\\d{6}","\\d{9,10}",,,"1781234567",,,[9,10]],[,,"55\\d{8}","\\d{10}",,,"5512345678",,
,[10]],"IT",39,"00",,,,,,,,[[,"(\\d{2})(\\d{3,4})(\\d{4})","$1 $2 $3",["0[26]|55"]],[,"(0[26])(\\d{4})(\\d{5})","$1 $2 $3",["0[26]"]],[,"(0[26])(\\d{4,6})","$1 $2",["0[26]"]],[,"(0\\d{2})(\\d{3,4})(\\d{4})","$1 $2 $3",["0[13-57-9][0159]"]],[,"(\\d{3})(\\d{3,6})","$1 $2",["0[13-57-9][0159]|8(?:03|4[17]|9[245])","0[13-57-9][0159]|8(?:03|4[17]|9(?:2|[45][0-4]))"]],[,"(0\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["0[13-57-9][2-46-8]"]],[,"(0\\d{3})(\\d{2,6})","$1 $2",["0[13-57-9][2-46-8]"]],[,"(\\d{3})(\\d{3})(\\d{3,4})",
"$1 $2 $3",["[13]|8(?:00|4[08]|9[59])","[13]|8(?:00|4[08]|9(?:5[5-9]|9))"]],[,"(\\d{4})(\\d{4})","$1 $2",["894","894[5-9]"]],[,"(\\d{3})(\\d{4})(\\d{4})","$1 $2 $3",["3"]]],,[,,"NA","NA",,,,,,[-1]],1,,[,,"848\\d{6}","\\d{9}",,,"848123456",,,[9]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],JE:[,[,,"[135789]\\d{6,9}","\\d{6,10}",,,,,,[10],[6]],[,,"1534\\d{6}","\\d{6,10}",,,"1534456789"],[,,"7(?:509|7(?:00|97)|829|937)\\d{6}","\\d{10}",,,"7797123456"],[,,"80(?:07(?:35|81)|8901)\\d{4}","\\d{10}",
,,"8007354567"],[,,"(?:871206|90(?:066[59]|1810|71(?:07|55)))\\d{4}","\\d{10}",,,"9018105678"],[,,"8(?:4(?:4(?:4(?:05|42|69)|703)|5(?:041|800))|70002)\\d{4}","\\d{10}",,,"8447034567"],[,,"701511\\d{4}","\\d{10}",,,"7015115678"],[,,"56\\d{8}","\\d{10}",,,"5612345678"],"JE",44,"00","0"," x",,"0",,,,,,[,,"76(?:0[012]|2[356]|4[0134]|5[49]|6[0-369]|77|81|9[39])\\d{6}","\\d{10}",,,"7640123456"],,,[,,"NA","NA",,,,,,[-1]],[,,"3(?:0(?:07(?:35|81)|8901)|3\\d{4}|4(?:4(?:4(?:05|42|69)|703)|5(?:041|800))|7(?:0002|1206))\\d{4}|55\\d{8}",
"\\d{10}",,,"5512345678"],,,[,,"NA","NA",,,,,,[-1]]],JM:[,[,,"[589]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"876(?:5(?:0[12]|1[0-468]|2[35]|63)|6(?:0[1-3579]|1[027-9]|[23]\\d|40|5[06]|6[2-589]|7[05]|8[04]|9[4-9])|7(?:0[2-689]|[1-6]\\d|8[056]|9[45])|9(?:0[1-8]|1[02378]|[2-8]\\d|9[2-468]))\\d{4}","\\d{7}(?:\\d{3})?",,,"8765123456"],[,,"876(?:2(?:[16-9]\\d|58)|[348]\\d{2}|5(?:0[3-9]|2[0-246-9]|6[0-24-9]|[3-578]\\d)|7(?:0[07]|7\\d|8[1-47-9]|9[0-36-9])|9(?:[01]9|9[0579]))\\d{4}","\\d{7}(?:\\d{3})?",
,,"8762101234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"JM",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"876",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],JO:[,[,,"[235-9]\\d{7,8}","\\d{8,9}",,,,,,[8,9]],[,,"(?:2(?:6(?:2[0-35-9]|3[0-57-8]|4[24-7]|5[0-24-8]|[6-8][023]|9[0-3])|7(?:0[1-79]|10|2[014-7]|3[0-689]|4[019]|5[0-3578]))|32(?:0[1-69]|1[1-35-7]|2[024-7]|3\\d|4[0-3]|[57][023]|6[03])|53(?:0[0-3]|[13][023]|2[0-59]|49|5[0-35-9]|6[15]|7[45]|8[1-6]|9[0-36-9])|6(?:2[50]0|3(?:00|33)|4(?:0[0125]|1[2-7]|2[0569]|[38][07-9]|4[025689]|6[0-589]|7\\d|9[0-2])|5(?:[01][056]|2[034]|3[0-57-9]|4[17-8]|5[0-69]|6[0-35-9]|7[1-379]|8[0-68]|9[02-39]))|87(?:[02]0|7[08]|90))\\d{4}",
"\\d{8}",,,"62001234",,,[8]],[,,"7(?:55|7[025-9]|8[015-9]|9[0-25-9])\\d{6}","\\d{9}",,,"790123456",,,[9]],[,,"80\\d{6}","\\d{8}",,,"80012345",,,[8]],[,,"900\\d{5}","\\d{8}",,,"90012345",,,[8]],[,,"85\\d{6}","\\d{8}",,,"85012345",,,[8]],[,,"70\\d{7}","\\d{9}",,,"700123456",,,[9]],[,,"NA","NA",,,,,,[-1]],"JO",962,"00","0",,,"0",,,,[[,"(\\d)(\\d{3})(\\d{4})","$1 $2 $3",["[2356]|87"],"(0$1)"],[,"(7)(\\d{4})(\\d{4})","$1 $2 $3",["7[457-9]"],"0$1"],[,"(\\d{3})(\\d{5,6})","$1 $2",["70|8[0158]|9"],"0$1"]],
,[,,"74(?:66|77)\\d{5}","\\d{9}",,,"746612345",,,[9]],,,[,,"NA","NA",,,,,,[-1]],[,,"8(?:10|8\\d)\\d{5}","\\d{8}",,,"88101234",,,[8]],,,[,,"NA","NA",,,,,,[-1]]],JP:[,[,,"[1-9]\\d{8,9}|00(?:[36]\\d{7,14}|7\\d{5,7}|8\\d{7})","\\d{8,17}",,,,,,[8,9,10,11,12,13,14,15,16,17]],[,,"(?:1(?:1[235-8]|2[3-6]|3[3-9]|4[2-6]|[58][2-8]|6[2-7]|7[2-9]|9[1-9])|2[2-9]\\d|[36][1-9]\\d|4(?:6[02-8]|[2-578]\\d|9[2-59])|5(?:6[1-9]|7[2-8]|[2-589]\\d)|7(?:3[4-9]|4[02-9]|[25-9]\\d)|8(?:3[2-9]|4[5-9]|5[1-9]|8[03-9]|[2679]\\d)|9(?:[679][1-9]|[2-58]\\d))\\d{6}",
"\\d{9}",,,"312345678",,,[9]],[,,"[7-9]0[1-9]\\d{7}","\\d{10}",,,"9012345678",,,[10]],[,,"120\\d{6}|800\\d{7}|00(?:37\\d{6,13}|66\\d{6,13}|777(?:[01]\\d{2}|5\\d{3}|8\\d{4})|882[1245]\\d{4})","\\d{8,17}",,,"120123456"],[,,"990\\d{6}","\\d{9}",,,"990123456",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"60\\d{7}","\\d{9}",,,"601234567",,,[9]],[,,"50[1-9]\\d{7}","\\d{10}",,,"5012345678",,,[10]],"JP",81,"010","0",,,"0",,,,[[,"(\\d{3})(\\d{3})(\\d{3})","$1-$2-$3",["(?:12|57|99)0"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{4})",
"$1-$2-$3",["800"],"0$1"],[,"(\\d{4})(\\d{4})","$1-$2",["0077"],"$1"],[,"(\\d{4})(\\d{2})(\\d{3,4})","$1-$2-$3",["0077"],"$1"],[,"(\\d{4})(\\d{2})(\\d{4})","$1-$2-$3",["0088"],"$1"],[,"(\\d{4})(\\d{3})(\\d{3,4})","$1-$2-$3",["00(?:37|66)"],"$1"],[,"(\\d{4})(\\d{4})(\\d{4,5})","$1-$2-$3",["00(?:37|66)"],"$1"],[,"(\\d{4})(\\d{5})(\\d{5,6})","$1-$2-$3",["00(?:37|66)"],"$1"],[,"(\\d{4})(\\d{6})(\\d{6,7})","$1-$2-$3",["00(?:37|66)"],"$1"],[,"(\\d{2})(\\d{4})(\\d{4})","$1-$2-$3",["[2579]0|80[1-9]"],"0$1"],
[,"(\\d{4})(\\d)(\\d{4})","$1-$2-$3",["1(?:26|3[79]|4[56]|5[4-68]|6[3-5])|5(?:76|97)|499|746|8(?:3[89]|63|47|51)|9(?:49|80|9[16])","1(?:267|3(?:7[247]|9[278])|4(?:5[67]|66)|5(?:47|58|64|8[67])|6(?:3[245]|48|5[4-68]))|5(?:76|97)9|499[2468]|7468|8(?:3(?:8[78]|96)|636|477|51[24])|9(?:496|802|9(?:1[23]|69))","1(?:267|3(?:7[247]|9[278])|4(?:5[67]|66)|5(?:47|58|64|8[67])|6(?:3[245]|48|5[4-68]))|5(?:769|979[2-69])|499[2468]|7468|8(?:3(?:8[78]|96[2457-9])|636[2-57-9]|477|51[24])|9(?:496|802|9(?:1[23]|69))"],
"0$1"],[,"(\\d{3})(\\d{2})(\\d{4})","$1-$2-$3",["1(?:2[3-6]|3[3-9]|4[2-6]|5[2-8]|[68][2-7]|7[2-689]|9[1-578])|2(?:2[03-689]|3[3-58]|4[0-468]|5[04-8]|6[013-8]|7[06-9]|8[02-57-9]|9[13])|4(?:2[28]|3[689]|6[035-7]|7[05689]|80|9[3-5])|5(?:3[1-36-9]|4[4578]|5[013-8]|6[1-9]|7[2-8]|8[14-7]|9[4-9])|7(?:2[15]|3[5-9]|4[02-9]|6[135-8]|7[0-4689]|9[014-9])|8(?:2[49]|3[3-8]|4[5-8]|5[2-9]|6[35-9]|7[579]|8[03-579]|9[2-8])|9(?:[23]0|4[02-46-9]|5[024-79]|6[4-9]|7[2-47-9]|8[02-7]|9[3-7])","1(?:2[3-6]|3[3-9]|4[2-6]|5(?:[236-8]|[45][2-69])|[68][2-7]|7[2-689]|9[1-578])|2(?:2(?:[04-689]|3[23])|3[3-58]|4[0-468]|5(?:5[78]|7[2-4]|[0468][2-9])|6(?:[0135-8]|4[2-5])|7(?:[0679]|8[2-7])|8(?:[024578]|3[25-9]|9[6-9])|9(?:11|3[2-4]))|4(?:2(?:2[2-9]|8[237-9])|3[689]|6[035-7]|7(?:[059][2-8]|[68])|80|9[3-5])|5(?:3[1-36-9]|4[4578]|5[013-8]|6[1-9]|7[2-8]|8[14-7]|9(?:[89][2-8]|[4-7]))|7(?:2[15]|3[5-9]|4[02-9]|6[135-8]|7[0-4689]|9(?:[017-9]|4[6-8]|5[2-478]|6[2-589]))|8(?:2(?:4[4-8]|9[2-8])|3(?:7[2-6]|[3-6][2-9]|8[2-5])|4[5-8]|5[2-9]|6(?:[37]|5[4-7]|6[2-9]|8[2-8]|9[236-9])|7[579]|8[03-579]|9[2-8])|9(?:[23]0|4[02-46-9]|5[024-79]|6[4-9]|7[2-47-9]|8[02-7]|9(?:3[34]|[4-7]))",
"1(?:2[3-6]|3[3-9]|4[2-6]|5(?:[236-8]|[45][2-69])|[68][2-7]|7[2-689]|9[1-578])|2(?:2(?:[04-689]|3[23])|3[3-58]|4[0-468]|5(?:5[78]|7[2-4]|[0468][2-9])|6(?:[0135-8]|4[2-5])|7(?:[0679]|8[2-7])|8(?:[024578]|3[25-9]|9[6-9])|9(?:11|3[2-4]))|4(?:2(?:2[2-9]|8[237-9])|3[689]|6[035-7]|7(?:[059][2-8]|[68])|80|9[3-5])|5(?:3[1-36-9]|4[4578]|5[013-8]|6[1-9]|7[2-8]|8[14-7]|9(?:[89][2-8]|[4-7]))|7(?:2[15]|3[5-9]|4[02-9]|6[135-8]|7[0-4689]|9(?:[017-9]|4[6-8]|5[2-478]|6[2-589]))|8(?:2(?:4[4-8]|9(?:[3578]|20|4[04-9]|6[56]))|3(?:7(?:[2-5]|6[0-59])|[3-6][2-9]|8[2-5])|4[5-8]|5[2-9]|6(?:[37]|5(?:[467]|5[014-9])|6(?:[2-8]|9[02-69])|8[2-8]|9(?:[236-8]|9[23]))|7[579]|8[03-579]|9[2-8])|9(?:[23]0|4[02-46-9]|5[024-79]|6[4-9]|7[2-47-9]|8[02-7]|9(?:3(?:3[02-9]|4[0-24689])|4[2-69]|[5-7]))",
"1(?:2[3-6]|3[3-9]|4[2-6]|5(?:[236-8]|[45][2-69])|[68][2-7]|7[2-689]|9[1-578])|2(?:2(?:[04-689]|3[23])|3[3-58]|4[0-468]|5(?:5[78]|7[2-4]|[0468][2-9])|6(?:[0135-8]|4[2-5])|7(?:[0679]|8[2-7])|8(?:[024578]|3[25-9]|9[6-9])|9(?:11|3[2-4]))|4(?:2(?:2[2-9]|8[237-9])|3[689]|6[035-7]|7(?:[059][2-8]|[68])|80|9[3-5])|5(?:3[1-36-9]|4[4578]|5[013-8]|6[1-9]|7[2-8]|8[14-7]|9(?:[89][2-8]|[4-7]))|7(?:2[15]|3[5-9]|4[02-9]|6[135-8]|7[0-4689]|9(?:[017-9]|4[6-8]|5[2-478]|6[2-589]))|8(?:2(?:4[4-8]|9(?:[3578]|20|4[04-9]|6(?:5[25]|60)))|3(?:7(?:[2-5]|6[0-59])|[3-6][2-9]|8[2-5])|4[5-8]|5[2-9]|6(?:[37]|5(?:[467]|5[014-9])|6(?:[2-8]|9[02-69])|8[2-8]|9(?:[236-8]|9[23]))|7[579]|8[03-579]|9[2-8])|9(?:[23]0|4[02-46-9]|5[024-79]|6[4-9]|7[2-47-9]|8[02-7]|9(?:3(?:3[02-9]|4[0-24689])|4[2-69]|[5-7]))"],
"0$1"],[,"(\\d{2})(\\d{3})(\\d{4})","$1-$2-$3",["1|2(?:2[37]|5[5-9]|64|78|8[39]|91)|4(?:2[2689]|64|7[347])|5(?:[2-589]|39)|60|8(?:[46-9]|3[279]|2[124589])|9(?:[235-8]|93)","1|2(?:2[37]|5(?:[57]|[68]0|9[19])|64|78|8[39]|917)|4(?:2(?:[68]|20|9[178])|64|7[347])|5(?:[2-589]|39[67])|60|8(?:[46-9]|3[279]|2[124589])|9(?:[235-8]|93[34])","1|2(?:2[37]|5(?:[57]|[68]0|9(?:17|99))|64|78|8[39]|917)|4(?:2(?:[68]|20|9[178])|64|7[347])|5(?:[2-589]|39[67])|60|8(?:[46-9]|3[279]|2[124589])|9(?:[235-8]|93(?:31|4))"],
"0$1"],[,"(\\d{3})(\\d{2})(\\d{4})","$1-$2-$3",["2(?:9[14-79]|74|[34]7|[56]9)|82|993"],"0$1"],[,"(\\d)(\\d{4})(\\d{4})","$1-$2-$3",["3|4(?:2[09]|7[01])|6[1-9]"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{4})","$1-$2-$3",["[2479][1-9]"],"0$1"]],[[,"(\\d{3})(\\d{3})(\\d{3})","$1-$2-$3",["(?:12|57|99)0"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{4})","$1-$2-$3",["800"],"0$1"],[,"(\\d{2})(\\d{4})(\\d{4})","$1-$2-$3",["[2579]0|80[1-9]"],"0$1"],[,"(\\d{4})(\\d)(\\d{4})","$1-$2-$3",["1(?:26|3[79]|4[56]|5[4-68]|6[3-5])|5(?:76|97)|499|746|8(?:3[89]|63|47|51)|9(?:49|80|9[16])",
"1(?:267|3(?:7[247]|9[278])|4(?:5[67]|66)|5(?:47|58|64|8[67])|6(?:3[245]|48|5[4-68]))|5(?:76|97)9|499[2468]|7468|8(?:3(?:8[78]|96)|636|477|51[24])|9(?:496|802|9(?:1[23]|69))","1(?:267|3(?:7[247]|9[278])|4(?:5[67]|66)|5(?:47|58|64|8[67])|6(?:3[245]|48|5[4-68]))|5(?:769|979[2-69])|499[2468]|7468|8(?:3(?:8[78]|96[2457-9])|636[2-57-9]|477|51[24])|9(?:496|802|9(?:1[23]|69))"],"0$1"],[,"(\\d{3})(\\d{2})(\\d{4})","$1-$2-$3",["1(?:2[3-6]|3[3-9]|4[2-6]|5[2-8]|[68][2-7]|7[2-689]|9[1-578])|2(?:2[03-689]|3[3-58]|4[0-468]|5[04-8]|6[013-8]|7[06-9]|8[02-57-9]|9[13])|4(?:2[28]|3[689]|6[035-7]|7[05689]|80|9[3-5])|5(?:3[1-36-9]|4[4578]|5[013-8]|6[1-9]|7[2-8]|8[14-7]|9[4-9])|7(?:2[15]|3[5-9]|4[02-9]|6[135-8]|7[0-4689]|9[014-9])|8(?:2[49]|3[3-8]|4[5-8]|5[2-9]|6[35-9]|7[579]|8[03-579]|9[2-8])|9(?:[23]0|4[02-46-9]|5[024-79]|6[4-9]|7[2-47-9]|8[02-7]|9[3-7])",
"1(?:2[3-6]|3[3-9]|4[2-6]|5(?:[236-8]|[45][2-69])|[68][2-7]|7[2-689]|9[1-578])|2(?:2(?:[04-689]|3[23])|3[3-58]|4[0-468]|5(?:5[78]|7[2-4]|[0468][2-9])|6(?:[0135-8]|4[2-5])|7(?:[0679]|8[2-7])|8(?:[024578]|3[25-9]|9[6-9])|9(?:11|3[2-4]))|4(?:2(?:2[2-9]|8[237-9])|3[689]|6[035-7]|7(?:[059][2-8]|[68])|80|9[3-5])|5(?:3[1-36-9]|4[4578]|5[013-8]|6[1-9]|7[2-8]|8[14-7]|9(?:[89][2-8]|[4-7]))|7(?:2[15]|3[5-9]|4[02-9]|6[135-8]|7[0-4689]|9(?:[017-9]|4[6-8]|5[2-478]|6[2-589]))|8(?:2(?:4[4-8]|9[2-8])|3(?:7[2-6]|[3-6][2-9]|8[2-5])|4[5-8]|5[2-9]|6(?:[37]|5[4-7]|6[2-9]|8[2-8]|9[236-9])|7[579]|8[03-579]|9[2-8])|9(?:[23]0|4[02-46-9]|5[024-79]|6[4-9]|7[2-47-9]|8[02-7]|9(?:3[34]|[4-7]))",
"1(?:2[3-6]|3[3-9]|4[2-6]|5(?:[236-8]|[45][2-69])|[68][2-7]|7[2-689]|9[1-578])|2(?:2(?:[04-689]|3[23])|3[3-58]|4[0-468]|5(?:5[78]|7[2-4]|[0468][2-9])|6(?:[0135-8]|4[2-5])|7(?:[0679]|8[2-7])|8(?:[024578]|3[25-9]|9[6-9])|9(?:11|3[2-4]))|4(?:2(?:2[2-9]|8[237-9])|3[689]|6[035-7]|7(?:[059][2-8]|[68])|80|9[3-5])|5(?:3[1-36-9]|4[4578]|5[013-8]|6[1-9]|7[2-8]|8[14-7]|9(?:[89][2-8]|[4-7]))|7(?:2[15]|3[5-9]|4[02-9]|6[135-8]|7[0-4689]|9(?:[017-9]|4[6-8]|5[2-478]|6[2-589]))|8(?:2(?:4[4-8]|9(?:[3578]|20|4[04-9]|6[56]))|3(?:7(?:[2-5]|6[0-59])|[3-6][2-9]|8[2-5])|4[5-8]|5[2-9]|6(?:[37]|5(?:[467]|5[014-9])|6(?:[2-8]|9[02-69])|8[2-8]|9(?:[236-8]|9[23]))|7[579]|8[03-579]|9[2-8])|9(?:[23]0|4[02-46-9]|5[024-79]|6[4-9]|7[2-47-9]|8[02-7]|9(?:3(?:3[02-9]|4[0-24689])|4[2-69]|[5-7]))",
"1(?:2[3-6]|3[3-9]|4[2-6]|5(?:[236-8]|[45][2-69])|[68][2-7]|7[2-689]|9[1-578])|2(?:2(?:[04-689]|3[23])|3[3-58]|4[0-468]|5(?:5[78]|7[2-4]|[0468][2-9])|6(?:[0135-8]|4[2-5])|7(?:[0679]|8[2-7])|8(?:[024578]|3[25-9]|9[6-9])|9(?:11|3[2-4]))|4(?:2(?:2[2-9]|8[237-9])|3[689]|6[035-7]|7(?:[059][2-8]|[68])|80|9[3-5])|5(?:3[1-36-9]|4[4578]|5[013-8]|6[1-9]|7[2-8]|8[14-7]|9(?:[89][2-8]|[4-7]))|7(?:2[15]|3[5-9]|4[02-9]|6[135-8]|7[0-4689]|9(?:[017-9]|4[6-8]|5[2-478]|6[2-589]))|8(?:2(?:4[4-8]|9(?:[3578]|20|4[04-9]|6(?:5[25]|60)))|3(?:7(?:[2-5]|6[0-59])|[3-6][2-9]|8[2-5])|4[5-8]|5[2-9]|6(?:[37]|5(?:[467]|5[014-9])|6(?:[2-8]|9[02-69])|8[2-8]|9(?:[236-8]|9[23]))|7[579]|8[03-579]|9[2-8])|9(?:[23]0|4[02-46-9]|5[024-79]|6[4-9]|7[2-47-9]|8[02-7]|9(?:3(?:3[02-9]|4[0-24689])|4[2-69]|[5-7]))"],
"0$1"],[,"(\\d{2})(\\d{3})(\\d{4})","$1-$2-$3",["1|2(?:2[37]|5[5-9]|64|78|8[39]|91)|4(?:2[2689]|64|7[347])|5(?:[2-589]|39)|60|8(?:[46-9]|3[279]|2[124589])|9(?:[235-8]|93)","1|2(?:2[37]|5(?:[57]|[68]0|9[19])|64|78|8[39]|917)|4(?:2(?:[68]|20|9[178])|64|7[347])|5(?:[2-589]|39[67])|60|8(?:[46-9]|3[279]|2[124589])|9(?:[235-8]|93[34])","1|2(?:2[37]|5(?:[57]|[68]0|9(?:17|99))|64|78|8[39]|917)|4(?:2(?:[68]|20|9[178])|64|7[347])|5(?:[2-589]|39[67])|60|8(?:[46-9]|3[279]|2[124589])|9(?:[235-8]|93(?:31|4))"],
"0$1"],[,"(\\d{3})(\\d{2})(\\d{4})","$1-$2-$3",["2(?:9[14-79]|74|[34]7|[56]9)|82|993"],"0$1"],[,"(\\d)(\\d{4})(\\d{4})","$1-$2-$3",["3|4(?:2[09]|7[01])|6[1-9]"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{4})","$1-$2-$3",["[2479][1-9]"],"0$1"]],[,,"20\\d{8}","\\d{10}",,,"2012345678",,,[10]],,,[,,"00(?:37\\d{6,13}|66\\d{6,13}|777(?:[01]\\d{2}|5\\d{3}|8\\d{4})|882[1245]\\d{4})","\\d{8,17}",,,"00777012"],[,,"570\\d{6}","\\d{9}",,,"570123456",,,[9]],1,,[,,"NA","NA",,,,,,[-1]]],KE:[,[,,"20\\d{6,7}|[4-9]\\d{6,9}","\\d{7,10}",
,,,,,[7,8,9,10]],[,,"20\\d{6,7}|4(?:[0136]\\d{7}|[245]\\d{5,7})|5(?:[08]\\d{7}|[1-79]\\d{5,7})|6(?:[01457-9]\\d{5,7}|[26]\\d{7})","\\d{7,9}",,,"202012345",,,[7,8,9]],[,,"7(?:[0-369]\\d|4[0-2]|5[0-6]|7[0-7]|8[0-25-9])\\d{6}","\\d{9}",,,"712123456",,,[9]],[,,"800[24-8]\\d{5,6}","\\d{9,10}",,,"800223456",,,[9,10]],[,,"900[02-9]\\d{5}","\\d{9}",,,"900223456",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"KE",254,"000","0",,,"005|0",,,,[[,"(\\d{2})(\\d{5,7})","$1 $2",["[24-6]"],
"0$1"],[,"(\\d{3})(\\d{6})","$1 $2",["7"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{3,4})","$1 $2 $3",["[89]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],KG:[,[,,"[235-8]\\d{8,9}","\\d{5,10}",,,,,,[9,10],[5,6]],[,,"(?:3(?:1(?:[256]\\d|3[1-9]|47)|2(?:22|3[0-479]|6[0-7])|4(?:22|5[6-9]|6\\d)|5(?:22|3[4-7]|59|6\\d)|6(?:22|5[35-7]|6\\d)|7(?:22|3[468]|4[1-9]|59|[67]\\d)|9(?:22|4[1-8]|6\\d))|6(?:09|12|2[2-4])\\d)\\d{5}","\\d{5,10}",,,"312123456",,,
[9]],[,,"(?:20[0-35]|5[124-7]\\d|7[07]\\d)\\d{6}","\\d{9}",,,"700123456",,,[9]],[,,"800\\d{6,7}","\\d{9,10}",,,"800123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"KG",996,"00","0",,,"0",,,,[[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3",["[25-7]|31[25]"],"0$1"],[,"(\\d{4})(\\d{5})","$1 $2",["3(?:1[36]|[2-9])"],"0$1"],[,"(\\d{3})(\\d{3})(\\d)(\\d{3})","$1 $2 $3 $4",["8"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,
[-1]],,,[,,"NA","NA",,,,,,[-1]]],KH:[,[,,"[1-9]\\d{7,9}","\\d{6,10}",,,,,,[8,9,10],[6,7]],[,,"(?:2[3-6]|3[2-6]|4[2-4]|[5-7][2-5])(?:[237-9]|4[56]|5\\d|6\\d?)\\d{5}|23(?:4[234]|8\\d{2})\\d{4}","\\d{6,9}",,,"23756789",,,[8,9]],[,,"(?:1(?:[013-79]\\d|[28]\\d{1,2})|2[3-6]48|3(?:[18]\\d{2}|[2-6]48)|4[2-4]48|5[2-5]48|6(?:[016-9]\\d|[2-5]48)|7(?:[07-9]\\d|[16]\\d{2}|[2-5]48)|8(?:[013-79]\\d|8\\d{2})|9(?:6\\d{2}|7\\d{1,2}|[0-589]\\d))\\d{5}","\\d{8,9}",,,"91234567",,,[8,9]],[,,"1800(?:1\\d|2[019])\\d{4}",
"\\d{10}",,,"1800123456",,,[10]],[,,"1900(?:1\\d|2[09])\\d{4}","\\d{10}",,,"1900123456",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"KH",855,"00[14-9]","0",,,"0",,,,[[,"(\\d{2})(\\d{3})(\\d{3,4})","$1 $2 $3",["1\\d[1-9]|[2-9]"],"0$1"],[,"(1[89]00)(\\d{3})(\\d{3})","$1 $2 $3",["1[89]0"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],KI:[,[,,"[2458]\\d{4}|3\\d{4,7}|7\\d{7}","\\d{5,8}",,,,,,[5,8]],[,,"(?:[24]\\d|3[1-9]|50|8[0-5])\\d{3}",
"\\d{5}",,,"31234",,,[5]],[,,"7\\d{7}","\\d{8}",,,"72012345",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"3001\\d{4}","\\d{5,8}",,,"30010000",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"KI",686,"00",,,,"0",,,,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],KM:[,[,,"[379]\\d{6}","\\d{7}",,,,,,[7]],[,,"7(?:6[0-37-9]|7[0-57-9])\\d{4}","\\d{7}",,,"7712345"],[,,"3[234]\\d{5}","\\d{7}",,,"3212345"],[,,"NA","NA",,,,,,[-1]],
[,,"(?:39[01]|9[01]0)\\d{4}","\\d{7}",,,"9001234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"KM",269,"00",,,,,,,,[[,"(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],KN:[,[,,"[589]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"869(?:2(?:29|36)|302|4(?:6[015-9]|70))\\d{4}","\\d{7}(?:\\d{3})?",,,"8692361234"],[,,"869(?:5(?:5[6-8]|6[5-7])|66\\d|76[02-7])\\d{4}","\\d{7}(?:\\d{3})?",
,,"8697652917"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"KN",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"869",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],KP:[,[,,"1\\d{9}|[28]\\d{7}","\\d{6,8}|\\d{10}",,,,,,[8,10],[6,7]],[,,"2\\d{7}|85\\d{6}","\\d{6,8}",,,"21234567",,,[8]],[,,"19[123]\\d{7}",
"\\d{10}",,,"1921234567",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"KP",850,"00|99","0",,,"0",,,,[[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["1"],"0$1"],[,"(\\d)(\\d{3})(\\d{4})","$1 $2 $3",["2"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["8"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"2(?:[0-24-9]\\d{2}|3(?:[0-79]\\d|8[02-9]))\\d{4}","\\d{8}",,,"23821234",,,[8]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],
KR:[,[,,"007\\d{9,11}|[1-7]\\d{3,9}|8\\d{8}","\\d{3,14}",,,,,,[4,5,6,8,9,10,12,13,14],[3,7]],[,,"(?:2|3[1-3]|[46][1-4]|5[1-5])(?:1\\d{2,3}|[1-9]\\d{6,7})","\\d{3,10}",,,"22123456",,,[4,5,6,8,9,10]],[,,"1[0-26-9]\\d{7,8}","\\d{9,10}",,,"1000000000",,,[9,10]],[,,"(?:00798\\d{0,2}|80)\\d{7}","\\d{9,14}",,,"801234567",,,[9,12,13,14]],[,,"60[2-9]\\d{6}","\\d{9}",,,"602345678",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"50\\d{8}","\\d{10}",,,"5012345678",,,[10]],[,,"70\\d{8}","\\d{10}",,,"7012345678",,,[10]],"KR",
82,"00(?:[124-68]|3\\d{2}|7(?:[0-8]\\d|9[0-79]))","0",,,"0(8[1-46-8]|85\\d{2})?",,,,[[,"(\\d{5})(\\d{3,4})(\\d{4})","$1 $2 $3",["00798"],"$1","0$CC-$1"],[,"(\\d{5})(\\d{2})(\\d{3})(\\d{4})","$1 $2 $3 $4",["00798"],"$1","0$CC-$1"],[,"(\\d{2})(\\d{4})(\\d{4})","$1-$2-$3",["1(?:0|1[19]|[69]9|5[458])|[57]0","1(?:0|1[19]|[69]9|5(?:44|59|8))|[57]0"],"0$1","0$CC-$1"],[,"(\\d{2})(\\d{3,4})(\\d{4})","$1-$2-$3",["1(?:[01]|5[1-4]|6[2-8]|[7-9])|[68]0|[3-6][1-9][1-9]","1(?:[01]|5(?:[1-3]|4[56])|6[2-8]|[7-9])|[68]0|[3-6][1-9][1-9]"],
"0$1","0$CC-$1"],[,"(\\d{3})(\\d)(\\d{4})","$1-$2-$3",["131","1312"],"0$1","0$CC-$1"],[,"(\\d{3})(\\d{2})(\\d{4})","$1-$2-$3",["131","131[13-9]"],"0$1","0$CC-$1"],[,"(\\d{3})(\\d{3})(\\d{4})","$1-$2-$3",["13[2-9]"],"0$1","0$CC-$1"],[,"(\\d{2})(\\d{2})(\\d{3})(\\d{4})","$1-$2-$3-$4",["30"],"0$1","0$CC-$1"],[,"(\\d)(\\d{3,4})(\\d{4})","$1-$2-$3",["2[1-9]"],"0$1","0$CC-$1"],[,"(\\d)(\\d{3,4})","$1-$2",["21[0-46-9]"],"0$1","0$CC-$1"],[,"(\\d{2})(\\d{3,4})","$1-$2",["[3-6][1-9]1","[3-6][1-9]1(?:[0-46-9])"],
"0$1","0$CC-$1"],[,"(\\d{4})(\\d{4})","$1-$2",["1(?:5[46-9]|6[04678]|8[03579])","1(?:5(?:44|66|77|88|99)|6(?:00|44|6[16]|70|88)|8(?:00|33|55|77|99))"],"$1","0$CC-$1"]],[[,"(\\d{2})(\\d{4})(\\d{4})","$1-$2-$3",["1(?:0|1[19]|[69]9|5[458])|[57]0","1(?:0|1[19]|[69]9|5(?:44|59|8))|[57]0"],"0$1","0$CC-$1"],[,"(\\d{2})(\\d{3,4})(\\d{4})","$1-$2-$3",["1(?:[01]|5[1-4]|6[2-8]|[7-9])|[68]0|[3-6][1-9][1-9]","1(?:[01]|5(?:[1-3]|4[56])|6[2-8]|[7-9])|[68]0|[3-6][1-9][1-9]"],"0$1","0$CC-$1"],[,"(\\d{3})(\\d)(\\d{4})",
"$1-$2-$3",["131","1312"],"0$1","0$CC-$1"],[,"(\\d{3})(\\d{2})(\\d{4})","$1-$2-$3",["131","131[13-9]"],"0$1","0$CC-$1"],[,"(\\d{3})(\\d{3})(\\d{4})","$1-$2-$3",["13[2-9]"],"0$1","0$CC-$1"],[,"(\\d{2})(\\d{2})(\\d{3})(\\d{4})","$1-$2-$3-$4",["30"],"0$1","0$CC-$1"],[,"(\\d)(\\d{3,4})(\\d{4})","$1-$2-$3",["2[1-9]"],"0$1","0$CC-$1"],[,"(\\d)(\\d{3,4})","$1-$2",["21[0-46-9]"],"0$1","0$CC-$1"],[,"(\\d{2})(\\d{3,4})","$1-$2",["[3-6][1-9]1","[3-6][1-9]1(?:[0-46-9])"],"0$1","0$CC-$1"],[,"(\\d{4})(\\d{4})",
"$1-$2",["1(?:5[46-9]|6[04678]|8[03579])","1(?:5(?:44|66|77|88|99)|6(?:00|44|6[16]|70|88)|8(?:00|33|55|77|99))"],"$1","0$CC-$1"]],[,,"15\\d{7,8}","\\d{9,10}",,,"1523456789",,,[9,10]],,,[,,"00798\\d{7,9}","\\d{12,14}",,,"007981234567",,,[12,13,14]],[,,"1(?:5(?:44|66|77|88|99)|6(?:00|44|6[16]|70|88)|8(?:00|33|55|77|99))\\d{4}","\\d{8}",,,"15441234",,,[8]],,,[,,"NA","NA",,,,,,[-1]]],KW:[,[,,"[12569]\\d{6,7}","\\d{7,8}",,,,,,[7,8]],[,,"(?:18\\d|2(?:[23]\\d{2}|4(?:[1-35-9]\\d|44)|5(?:0[034]|[2-46]\\d|5[1-3]|7[1-7])))\\d{4}",
"\\d{7,8}",,,"22345678"],[,,"(?:5(?:[05]\\d{2}|1[0-7]\\d|2(?:22|5[25])|66\\d)|6(?:0[034679]\\d|222|5[015-9]\\d|6\\d{2}|7[067]\\d|9[0369]\\d)|9(?:0[09]\\d|22\\d|4[01479]\\d|55\\d|6[0679]\\d|[79]\\d{2}|8[057-9]\\d))\\d{4}","\\d{8}",,,"50012345"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"KW",965,"00",,,,,,,,[[,"(\\d{4})(\\d{3,4})","$1 $2",["[16]|2(?:[0-35-9]|4[0-35-9])|9[024-9]|52[25]"]],[,"(\\d{3})(\\d{5})","$1 $2",["244|5(?:[015]|66)"]]],
,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],KY:[,[,,"[3589]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"345(?:2(?:22|44)|444|6(?:23|38|40)|7(?:4[35-79]|6[6-9]|77)|8(?:00|1[45]|25|[48]8)|9(?:14|4[035-9]))\\d{4}","\\d{7}(?:\\d{3})?",,,"3452221234"],[,,"345(?:32[1-9]|5(?:1[67]|2[5-7]|4[6-8]|76)|9(?:1[67]|2[2-9]|3[689]))\\d{4}","\\d{7}(?:\\d{3})?",,,"3453231234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002345678"],[,,"900[2-9]\\d{6}|345976\\d{4}",
"\\d{10}",,,"9002345678"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"KY",1,"011","1",,,"1",,,,,,[,,"345849\\d{4}","\\d{10}",,,"3458491234"],,"345",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],KZ:[,[,,"(?:33\\d|7\\d{2}|80[09])\\d{7}","\\d{10}",,,,,,[10]],[,,"33622\\d{5}|7(?:1(?:0(?:[23]\\d|4[023]|59|63)|1(?:[23]\\d|4[0-79]|59)|2(?:[23]\\d|59)|3(?:2\\d|3[1-79]|4[0-35-9]|59)|4(?:2\\d|3[013-79]|4[0-8]|5[1-79])|5(?:2\\d|3[1-8]|4[1-7]|59)|6(?:[234]\\d|5[19]|61)|72\\d|8(?:[27]\\d|3[1-46-9]|4[0-5]))|2(?:1(?:[23]\\d|4[46-9]|5[3469])|2(?:2\\d|3[0679]|46|5[12679])|3(?:[234]\\d|5[139])|4(?:2\\d|3[1235-9]|59)|5(?:[23]\\d|4[01246-8]|59|61)|6(?:2\\d|3[1-9]|4[0-4]|59)|7(?:[2379]\\d|40|5[279])|8(?:[23]\\d|4[0-3]|59)|9(?:2\\d|3[124578]|59)))\\d{5}",
"\\d{10}",,,"7123456789"],[,,"7(?:0[012578]|47|6[02-4]|7[15-8]|85)\\d{7}","\\d{10}",,,"7710009998"],[,,"800\\d{7}","\\d{10}",,,"8001234567"],[,,"809\\d{7}","\\d{10}",,,"8091234567"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"751\\d{7}","\\d{10}",,,"7511234567"],"KZ",7,"810","8",,,"8",,"8~10",,,,[,,"NA","NA",,,,,,[-1]],,,[,,"751\\d{7}","\\d{10}",,,"7511234567"],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],LA:[,[,,"[2-8]\\d{7,9}","\\d{6,10}",,,,,,[8,9,10],[6]],[,,"(?:2[13]|3(?:0\\d|[14])|[5-7][14]|41|8[1468])\\d{6}",
"\\d{6,9}",,,"21212862",,,[8,9]],[,,"20(?:2[2389]|5[4-689]|7[6-8]|9[15-9])\\d{6}","\\d{10}",,,"2023123456",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"LA",856,"00","0",,,"0",,,,[[,"(20)(\\d{2})(\\d{3})(\\d{3})","$1 $2 $3 $4",["20"],"0$1"],[,"([2-8]\\d)(\\d{3})(\\d{3})","$1 $2 $3",["2[13]|3[14]|[4-8]"],"0$1"],[,"(30)(\\d{2})(\\d{2})(\\d{3})","$1 $2 $3 $4",["30"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,
,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],LB:[,[,,"[13-9]\\d{6,7}","\\d{7,8}",,,,,,[7,8]],[,,"(?:[14-6]\\d{2}|7(?:[2-57]\\d|62|8[0-7]|9[04-9])|8[02-9]\\d|9\\d{2})\\d{4}","\\d{7}",,,"1123456",,,[7]],[,,"(?:3\\d|7(?:[01]\\d|6[013-9]|8[89]|9[1-3])|81\\d)\\d{5}","\\d{7,8}",,,"71123456"],[,,"NA","NA",,,,,,[-1]],[,,"9[01]\\d{6}","\\d{8}",,,"90123456",,,[8]],[,,"80\\d{6}","\\d{8}",,,"80123456",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"LB",961,"00","0",,,"0",,,,[[,"(\\d)(\\d{3})(\\d{3})",
"$1 $2 $3",["[13-6]|7(?:[2-57]|62|8[0-7]|9[04-9])|8[02-9]|9"],"0$1"],[,"([7-9]\\d)(\\d{3})(\\d{3})","$1 $2 $3",["[89][01]|7(?:[01]|6[013-9]|8[89]|9[1-3])"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],LC:[,[,,"[5789]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"758(?:4(?:30|5[0-9]|6[2-9]|8[0-2])|57[0-2]|638)\\d{4}","\\d{7}(?:\\d{3})?",,,"7584305678"],[,,"758(?:28[4-7]|384|4(?:6[01]|8[4-9])|5(?:1[89]|20|84)|7(?:1[2-9]|2[0-8]))\\d{4}","\\d{7}(?:\\d{3})?",
,,"7582845678"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"LC",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"758",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],LI:[,[,,"6\\d{8}|[23789]\\d{6}","\\d{7,9}",,,,,,[7,9]],[,,"(?:2(?:01|1[27]|3\\d|6[02-578]|96)|3(?:7[0135-7]|8[048]|9[0269]))\\d{4}",
"\\d{7}",,,"2345678",,,[7]],[,,"6(?:51[01]|6(?:0[0-6]|2[016-9]|39))\\d{5}|7(?:[37-9]\\d|42|56)\\d{4}","\\d{7,9}",,,"660234567"],[,,"80(?:02[28]|9\\d{2})\\d{2}","\\d{7}",,,"8002222",,,[7]],[,,"90(?:02[258]|1(?:23|3[14])|66[136])\\d{2}","\\d{7}",,,"9002222",,,[7]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"LI",423,"00","0",,,"0",,,,[[,"(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3",["[23789]"]],[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3",["6[56]"]],[,"(69)(7\\d{2})(\\d{4})","$1 $2 $3",
["697"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"870(?:28|87)\\d{2}","\\d{7}",,,"8702812",,,[7]],,,[,,"697(?:42|56|[7-9]\\d)\\d{4}","\\d{9}",,,"697861234",,,[9]]],LK:[,[,,"[1-9]\\d{8}","\\d{7,9}",,,,,,[9],[7]],[,,"(?:[189]1|2[13-7]|3[1-8]|4[157]|5[12457]|6[35-7])[2-57]\\d{6}","\\d{7,9}",,,"112345678"],[,,"7[0125-8]\\d{7}","\\d{9}",,,"712345678"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"LK",94,"00","0",
,,"0",,,,[[,"(\\d{2})(\\d{1})(\\d{6})","$1 $2 $3",["[1-689]"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["7"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],LR:[,[,,"2\\d{7,8}|[378]\\d{8}|4\\d{6}|5\\d{6,8}","\\d{7,9}",,,,,,[7,8,9]],[,,"2\\d{7}","\\d{8}",,,"21234567",,,[8]],[,,"(?:20\\d{3}|330\\d{2}|4[67]\\d|5(?:55)?\\d{2}|77\\d{3}|88\\d{3})\\d{4}","\\d{7,9}",,,"770123456",,,[7,9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,
,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"332(?:02|[25]\\d)\\d{4}","\\d{9}",,,"332021234",,,[9]],"LR",231,"00","0",,,"0",,,,[[,"(2\\d)(\\d{3})(\\d{3})","$1 $2 $3",["2"],"0$1"],[,"([4-5])(\\d{3})(\\d{3})","$1 $2 $3",["[45]"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["[23578]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],LS:[,[,,"[2568]\\d{7}","\\d{8}",,,,,,[8]],[,,"2\\d{7}","\\d{8}",,,"22123456"],[,,"[56]\\d{7}","\\d{8}",
,,"50123456"],[,,"800[256]\\d{4}","\\d{8}",,,"80021234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"LS",266,"00",,,,,,,,[[,"(\\d{4})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],LT:[,[,,"[3-9]\\d{7}","\\d{8}",,,,,,[8]],[,,"(?:3[1478]|4[124-6]|52)\\d{6}","\\d{8}",,,"31234567"],[,,"6\\d{7}","\\d{8}",,,"61234567"],[,,"800\\d{5}","\\d{8}",,,"80012345"],[,,"9(?:0[0239]|10)\\d{5}",
"\\d{8}",,,"90012345"],[,,"808\\d{5}","\\d{8}",,,"80812345"],[,,"700\\d{5}","\\d{8}",,,"70012345"],[,,"NA","NA",,,,,,[-1]],"LT",370,"00","8",,,"[08]",,,,[[,"([34]\\d)(\\d{6})","$1 $2",["37|4(?:1|5[45]|6[2-4])"],"(8-$1)",,1],[,"([3-6]\\d{2})(\\d{5})","$1 $2",["3[148]|4(?:[24]|6[09])|528|6"],"(8-$1)",,1],[,"([7-9]\\d{2})(\\d{2})(\\d{3})","$1 $2 $3",["[7-9]"],"8 $1",,1],[,"(5)(2\\d{2})(\\d{4})","$1 $2 $3",["52[0-79]"],"(8-$1)",,1]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"70[67]\\d{5}",
"\\d{8}",,,"70712345"],,,[,,"NA","NA",,,,,,[-1]]],LU:[,[,,"[24-9]\\d{3,10}|3(?:[0-46-9]\\d{2,9}|5[013-9]\\d{1,8})","\\d{4,11}",,,,,,[4,5,6,7,8,9,10,11]],[,,"(?:2[2-9]\\d{2,9}|(?:[3457]\\d{2}|8(?:0[2-9]|[13-9]\\d)|9(?:0[89]|[2-579]\\d))\\d{1,8})","\\d{4,11}",,,"27123456"],[,,"6[2679][18]\\d{6}","\\d{9}",,,"628123456",,,[9]],[,,"800\\d{5}","\\d{8}",,,"80012345",,,[8]],[,,"90[015]\\d{5}","\\d{8}",,,"90012345",,,[8]],[,,"801\\d{5}","\\d{8}",,,"80112345",,,[8]],[,,"70\\d{6}","\\d{8}",,,"70123456",,,[8]],
[,,"20(?:1\\d{5}|[2-689]\\d{1,7})","\\d{4,10}",,,"20201234",,,[4,5,6,7,8,9,10]],"LU",352,"00",,,,"(15(?:0[06]|1[12]|35|4[04]|55|6[26]|77|88|99)\\d)",,,,[[,"(\\d{2})(\\d{3})","$1 $2",["[2-5]|7[1-9]|[89](?:[1-9]|0[2-9])"],,"$CC $1"],[,"(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3",["[2-5]|7[1-9]|[89](?:[1-9]|0[2-9])"],,"$CC $1"],[,"(\\d{2})(\\d{2})(\\d{3})","$1 $2 $3",["20"],,"$CC $1"],[,"(\\d{2})(\\d{2})(\\d{2})(\\d{1,2})","$1 $2 $3 $4",["2(?:[0367]|4[3-8])"],,"$CC $1"],[,"(\\d{2})(\\d{2})(\\d{2})(\\d{3})",
"$1 $2 $3 $4",["20"],,"$CC $1"],[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})(\\d{1,2})","$1 $2 $3 $4 $5",["2(?:[0367]|4[3-8])"],,"$CC $1"],[,"(\\d{2})(\\d{2})(\\d{2})(\\d{1,4})","$1 $2 $3 $4",["2(?:[12589]|4[12])|[3-5]|7[1-9]|8(?:[1-9]|0[2-9])|9(?:[1-9]|0[2-46-9])"],,"$CC $1"],[,"(\\d{3})(\\d{2})(\\d{3})","$1 $2 $3",["70|80[01]|90[015]"],,"$CC $1"],[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3",["6"],,"$CC $1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],
LV:[,[,,"[2689]\\d{7}","\\d{8}",,,,,,[8]],[,,"6\\d{7}","\\d{8}",,,"63123456"],[,,"2\\d{7}","\\d{8}",,,"21234567"],[,,"80\\d{6}","\\d{8}",,,"80123456"],[,,"90\\d{6}","\\d{8}",,,"90123456"],[,,"81\\d{6}","\\d{8}",,,"81123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"LV",371,"00",,,,,,,,[[,"([2689]\\d)(\\d{3})(\\d{3})","$1 $2 $3"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],LY:[,[,,"[25679]\\d{8}","\\d{7,9}",,,,,,[9],[7]],[,,"(?:2[1345]|5[1347]|6[123479]|71)\\d{7}",
"\\d{7,9}",,,"212345678"],[,,"9[1-6]\\d{7}","\\d{9}",,,"912345678"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"LY",218,"00","0",,,"0",,,,[[,"([25679]\\d)(\\d{7})","$1-$2",,"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MA:[,[,,"[5-9]\\d{8}","\\d{9}",,,,,,[9]],[,,"5(?:2(?:(?:[015-7]\\d|2[02-9]|3[2-57]|4[2-8]|8[235-7])\\d|9(?:0\\d|[89]0))|3(?:(?:[0-4]\\d|[57][2-9]|6[2-8]|9[3-9])\\d|8(?:0\\d|[89]0))|(?:4[067]|5[03])\\d{2})\\d{4}",
"\\d{9}",,,"520123456"],[,,"(?:6(?:[0-79]\\d|8[0-247-9])|7(?:[07][07]|6[12]))\\d{6}","\\d{9}",,,"650123456"],[,,"80\\d{7}","\\d{9}",,,"801234567"],[,,"89\\d{7}","\\d{9}",,,"891234567"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"5924[01]\\d{4}","\\d{9}",,,"592401234"],"MA",212,"00","0",,,"0",,,,[[,"([5-7]\\d{2})(\\d{6})","$1-$2",["5(?:2[015-7]|3[0-4])|[67]"],"0$1"],[,"([58]\\d{3})(\\d{5})","$1-$2",["5(?:2[2-489]|3[5-9]|92)|892","5(?:2(?:[2-48]|90)|3(?:[5-79]|80)|924)|892"],"0$1"],[,"(5\\d{4})(\\d{4})",
"$1-$2",["5(?:29|38)","5(?:29|38)[89]"],"0$1"],[,"([5]\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["5(?:4[067]|5[03])"],"0$1"],[,"(8[09])(\\d{7})","$1-$2",["8(?:0|9[013-9])"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MC:[,[,,"[4689]\\d{7,8}","\\d{8,9}",,,,,,[8,9]],[,,"870\\d{5}|9[2-47-9]\\d{6}","\\d{8}",,,"99123456",,,[8]],[,,"6\\d{8}|4(?:4\\d|5[1-9])\\d{5}","\\d{8,9}",,,"612345678"],[,,"90\\d{6}","\\d{8}",,,"90123456",,,[8]],
[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"MC",377,"00","0",,,"0",,,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["9"],"$1"],[,"(\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["4"],"0$1"],[,"(6)(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4 $5",["6"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{2})","$1 $2 $3",["8"],"$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"8\\d{7}","\\d{8}",,,,,,[8]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MD:[,[,,"[235-9]\\d{7}","\\d{8}",
,,,,,[8]],[,,"(?:2[1-9]\\d|3[1-79]\\d|5(?:33|5[257]))\\d{5}","\\d{8}",,,"22212345"],[,,"(?:562|6\\d{2}|7(?:[189]\\d|6[07]|7[457-9]))\\d{5}","\\d{8}",,,"62112345"],[,,"800\\d{5}","\\d{8}",,,"80012345"],[,,"90[056]\\d{5}","\\d{8}",,,"90012345"],[,,"808\\d{5}","\\d{8}",,,"80812345"],[,,"NA","NA",,,,,,[-1]],[,,"3[08]\\d{6}","\\d{8}",,,"30123456"],"MD",373,"00","0",,,"0",,,,[[,"(\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["22|3"],"0$1"],[,"([25-7]\\d{2})(\\d{2})(\\d{3})","$1 $2 $3",["2[13-9]|[5-7]"],"0$1"],[,
"([89]\\d{2})(\\d{5})","$1 $2",["[89]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"803\\d{5}","\\d{8}",,,"80312345"],,,[,,"NA","NA",,,,,,[-1]]],ME:[,[,,"[2-9]\\d{7,8}","\\d{6,9}",,,,,,[8,9],[6]],[,,"(?:20[2-8]|3(?:0[2-7]|[12][35-7]|3[4-7])|4(?:0[2367]|1[267])|5(?:0[467]|1[267]|2[367]))\\d{5}","\\d{6,8}",,,"30234567",,,[8]],[,,"6(?:00\\d|3[24]\\d|61\\d|7(?:[0-8]\\d|9(?:[3-9]|[0-2]\\d))|[89]\\d{2})\\d{4}","\\d{8,9}",,,"67622901"],[,,"80\\d{6}","\\d{8}",,,"80080002",,,[8]],[,,"(?:9(?:4[1568]|5[178]))\\d{5}",
"\\d{8}",,,"94515151",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"78[1-9]\\d{5}","\\d{8}",,,"78108780",,,[8]],"ME",382,"00","0",,,"0",,,,[[,"(\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["[2-57-9]|6[036-9]","[2-57-9]|6(?:[03689]|7(?:[0-8]|9[3-9]))"],"0$1"],[,"(67)(9)(\\d{3})(\\d{3})","$1 $2 $3 $4",["679","679[0-2]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"77\\d{6}","\\d{8}",,,"77273012",,,[8]],,,[,,"NA","NA",,,,,,[-1]]],MF:[,[,,"[56]\\d{8}","\\d{9}",,,,,,[9]],[,,"590(?:[02][79]|13|5[0-268]|[78]7)\\d{4}",
"\\d{9}",,,"590271234"],[,,"690(?:0[0-7]|[1-9]\\d)\\d{4}","\\d{9}",,,"690301234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"MF",590,"00","0",,,"0",,,,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MG:[,[,,"[23]\\d{8}","\\d{7,9}",,,,,,[9],[7]],[,,"20(?:2\\d{2}|4[47]\\d|5[3467]\\d|6[279]\\d|7(?:2[29]|[35]\\d)|8[268]\\d|9[245]\\d)\\d{4}","\\d{7,9}",,,"202123456"],[,,
"3[2-49]\\d{7}","\\d{9}",,,"321234567"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"22\\d{7}","\\d{9}",,,"221234567"],"MG",261,"00","0",,,"0",,,,[[,"([23]\\d)(\\d{2})(\\d{3})(\\d{2})","$1 $2 $3 $4",,"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MH:[,[,,"[2-6]\\d{6}","\\d{7}",,,,,,[7]],[,,"(?:247|528|625)\\d{4}","\\d{7}",,,"2471234"],[,,"(?:235|329|45[56]|545)\\d{4}","\\d{7}",,,"2351234"],
[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"635\\d{4}","\\d{7}",,,"6351234"],"MH",692,"011","1",,,"1",,,,[[,"(\\d{3})(\\d{4})","$1-$2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MK:[,[,,"[2-578]\\d{7}","\\d{6,8}",,,,,,[8],[6,7]],[,,"(?:2(?:[23]\\d|5[124578]|6[01])|3(?:1[3-6]|[23][2-6]|4[2356])|4(?:[23][2-6]|4[3-6]|5[256]|6[25-8]|7[24-6]|8[4-6]))\\d{5}","\\d{6,8}",,,"22212345"],[,,"7(?:[0-25-8]\\d{2}|32\\d|421)\\d{4}",
"\\d{6,8}",,,"72345678"],[,,"800\\d{5}","\\d{6,8}",,,"80012345"],[,,"5[02-9]\\d{6}","\\d{6,8}",,,"50012345"],[,,"8(?:0[1-9]|[1-9]\\d)\\d{5}","\\d{6,8}",,,"80123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"MK",389,"00","0",,,"0",,,,[[,"(2)(\\d{3})(\\d{4})","$1 $2 $3",["2"],"0$1"],[,"([347]\\d)(\\d{3})(\\d{3})","$1 $2 $3",["[347]"],"0$1"],[,"([58]\\d{2})(\\d)(\\d{2})(\\d{2})","$1 $2 $3 $4",["[58]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA",
"NA",,,,,,[-1]]],ML:[,[,,"[246-9]\\d{7}","\\d{8}",,,,,,[8]],[,,"(?:2(?:0(?:2\\d|7[0-8])|1(?:2[5-7]|[3-689]\\d))|44[1239]\\d)\\d{4}","\\d{8}",,,"20212345"],[,,"(?:2(?:079|17\\d)|[679]\\d{3}|8[239]\\d{2})\\d{4}","\\d{8}",,,"65012345"],[,,"80\\d{6}","\\d{8}",,,"80012345"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"ML",223,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[246-9]"]],[,"(\\d{4})","$1",["67|74"]]],[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})",
"$1 $2 $3 $4",["[246-9]"]]],[,,"NA","NA",,,,,,[-1]],,,[,,"80\\d{6}","\\d{8}",,,"80012345"],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MM:[,[,,"[14578]\\d{5,7}|[26]\\d{5,8}|9(?:2\\d{0,2}|[58]|3\\d|4\\d{1,2}|6\\d?|[79]\\d{0,2})\\d{6}","\\d{5,10}",,,,,,[6,7,8,9,10],[5]],[,,"1(?:2\\d{1,2}|[3-5]\\d|6\\d?|[89][0-6]\\d)\\d{4}|2(?:2(?:000\\d{3}|\\d{4})|3\\d{4}|4(?:0\\d{5}|\\d{4})|5(?:1\\d{3,6}|[02-9]\\d{3,5})|[6-9]\\d{4})|4(?:2[245-8]|[346][2-6]|5[3-5])\\d{4}|5(?:2(?:20?|[3-8])|3[2-68]|4(?:21?|[4-8])|5[23]|6[2-4]|7[2-8]|8[24-7]|9[2-7])\\d{4}|6(?:0[23]|1[2356]|[24][2-6]|3[24-6]|5[2-4]|6[2-8]|7(?:[2367]|4\\d|5\\d?|8[145]\\d)|8[245]|9[24])\\d{4}|7(?:[04][24-8]|[15][2-7]|22|3[2-4])\\d{4}|8(?:1(?:2\\d{1,2}|[3-689]\\d)|2(?:2\\d|3(?:\\d|20)|[4-8]\\d)|3[24]\\d|4[24-7]\\d|5[245]\\d|6[23]\\d)\\d{3}",
"\\d{5,9}",,,"1234567",,,[6,7,8,9]],[,,"17[01]\\d{4}|9(?:2(?:[0-4]|5\\d{2}|6[0-5]\\d)|3[0-36]\\d|4(?:0[0-4]\\d|[1379]\\d|2\\d{2}|4[0-589]\\d|5\\d{2}|88)|5[0-6]|61?\\d|7(?:3\\d|[789]\\d{2})|8\\d|9(?:1\\d|[67]\\d{2}|[089]))\\d{5}","\\d{7,10}",,,"92123456",,,[7,8,9,10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"1333\\d{4}","\\d{8}",,,"13331234",,,[8]],"MM",95,"00","0",,,"0",,,,[[,"(\\d)(\\d{3})(\\d{3,4})","$1 $2 $3",["1|2[245]"],"0$1"],[,"(2)(\\d{4})(\\d{4})",
"$1 $2 $3",["251"],"0$1"],[,"(\\d)(\\d{2})(\\d{3})","$1 $2 $3",["16|2"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{3,4})","$1 $2 $3",["67|81"],"0$1"],[,"(\\d{2})(\\d{2})(\\d{3,4})","$1 $2 $3",["[4-8]"],"0$1"],[,"(9)(\\d{3})(\\d{4,6})","$1 $2 $3",["9(?:2[0-4]|[35-9]|4[137-9])"],"0$1"],[,"(9)([34]\\d{4})(\\d{4})","$1 $2 $3",["9(?:3[0-36]|4[0-57-9])"],"0$1"],[,"(9)(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3 $4",["92[56]"],"0$1"],[,"(9)(\\d{3})(\\d{3})(\\d{2})","$1 $2 $3 $4",["93"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,
"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MN:[,[,,"[12]\\d{7,9}|[57-9]\\d{7}","\\d{6,10}",,,,,,[8,9,10],[6,7]],[,,"[12](?:1\\d|2(?:[1-3]\\d?|7\\d)|3[2-8]\\d{1,2}|4[2-68]\\d{1,2}|5[1-4689]\\d{1,2})\\d{5}|5[0568]\\d{6}","\\d{6,10}",,,"50123456"],[,,"(?:8(?:[05689]\\d|3[01])|9[013-9]\\d)\\d{5}","\\d{8}",,,"88123456",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"7[05-8]\\d{6}","\\d{8}",,,"75123456",,,[8]],"MN",
976,"001","0",,,"0",,,,[[,"([12]\\d)(\\d{2})(\\d{4})","$1 $2 $3",["[12]1"],"0$1"],[,"([12]2\\d)(\\d{5,6})","$1 $2",["[12]2[1-3]"],"0$1"],[,"([12]\\d{3})(\\d{5})","$1 $2",["[12](?:27|[3-5])","[12](?:27|[3-5]\\d)2"],"0$1"],[,"(\\d{4})(\\d{4})","$1 $2",["[57-9]"],"$1"],[,"([12]\\d{4})(\\d{4,5})","$1 $2",["[12](?:27|[3-5])","[12](?:27|[3-5]\\d)[4-9]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MO:[,[,,"[268]\\d{7}","\\d{8}",,,,,,[8]],
[,,"(?:28[2-57-9]|8[2-57-9]\\d)\\d{5}","\\d{8}",,,"28212345"],[,,"6(?:[2356]\\d|8[158])\\d{5}","\\d{8}",,,"66123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"MO",853,"00",,,,,,,,[[,"([268]\\d{3})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MP:[,[,,"[5689]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"670(?:2(?:3[3-7]|56|8[5-8])|32[1238]|4(?:33|8[348])|5(?:32|55|88)|6(?:64|70|82)|78[589]|8[3-9]8|989)\\d{4}",
"\\d{7}(?:\\d{3})?",,,"6702345678"],[,,"670(?:2(?:3[3-7]|56|8[5-8])|32[1238]|4(?:33|8[348])|5(?:32|55|88)|6(?:64|70|82)|78[589]|8[3-9]8|989)\\d{4}","\\d{7}(?:\\d{3})?",,,"6702345678"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"MP",1,"011","1",,,"1",,,1,,,[,,"NA","NA",,,,,,[-1]],,"670",[,,"NA","NA",,,,,,[-1]],[,,"NA",
"NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MQ:[,[,,"[56]\\d{8}","\\d{9}",,,,,,[9]],[,,"596(?:0[2-5]|[12]0|3[05-9]|4[024-8]|[5-7]\\d|89|9[4-8])\\d{4}","\\d{9}",,,"596301234"],[,,"696(?:[0-479]\\d|5[013]|8[0-689])\\d{4}","\\d{9}",,,"696201234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"MQ",596,"00","0",,,"0",,,,[[,"(\\d{3})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",,"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],
[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MR:[,[,,"[2-48]\\d{7}","\\d{8}",,,,,,[8]],[,,"25[08]\\d{5}|35\\d{6}|45[1-7]\\d{5}","\\d{8}",,,"35123456"],[,,"[234][0-46-9]\\d{6}","\\d{8}",,,"22123456"],[,,"800\\d{5}","\\d{8}",,,"80012345"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"MR",222,"00",,,,,,,,[[,"([2-48]\\d)(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA",
"NA",,,,,,[-1]]],MS:[,[,,"[5689]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"664491\\d{4}","\\d{7}(?:\\d{3})?",,,"6644912345"],[,,"66449[2-6]\\d{4}","\\d{10}",,,"6644923456"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"MS",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"664",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,
,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MT:[,[,,"[2357-9]\\d{7}","\\d{8}",,,,,,[8]],[,,"2(?:0(?:1[0-6]|3[1-4]|[69]\\d)|[1-357]\\d{2})\\d{4}","\\d{8}",,,"21001234"],[,,"(?:7(?:210|[79]\\d{2})|9(?:2(?:1[01]|31)|696|8(?:1[1-3]|89|97)|9\\d{2}))\\d{4}","\\d{8}",,,"96961234"],[,,"800[3467]\\d{4}","\\d{8}",,,"80071234"],[,,"5(?:0(?:0(?:37|43)|6\\d{2}|70\\d|9[0168]\\d)|[12]\\d0[1-5])\\d{3}","\\d{8}",,,"50037123"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"3550\\d{4}","\\d{8}",,,"35501234"],"MT",356,
"00",,,,,,,,[[,"(\\d{4})(\\d{4})","$1 $2"]],,[,,"7117\\d{4}","\\d{8}",,,"71171234"],,,[,,"NA","NA",,,,,,[-1]],[,,"501\\d{5}","\\d{8}",,,"50112345"],,,[,,"NA","NA",,,,,,[-1]]],MU:[,[,,"[2-9]\\d{6,7}","\\d{7,8}",,,,,,[7,8]],[,,"(?:2(?:[03478]\\d|1[0-7]|6[1-69])|4(?:[013568]\\d|2[4-7])|5(?:44\\d|471)|6\\d{2}|8(?:14|3[129]))\\d{4}","\\d{7,8}",,,"2012345"],[,,"5(?:2[59]\\d|4(?:2[1-389]|4\\d|7[1-9]|9\\d)|7\\d{2}|8(?:[0-25689]\\d|7[15-8])|9[0-8]\\d)\\d{4}","\\d{8}",,,"52512345",,,[8]],[,,"80[012]\\d{4}",
"\\d{7}",,,"8001234",,,[7]],[,,"30\\d{5}","\\d{7}",,,"3012345",,,[7]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"3(?:20|9\\d)\\d{4}","\\d{7}",,,"3201234",,,[7]],"MU",230,"0(?:0|[2-7]0|33)",,,,,,"020",,[[,"([2-46-9]\\d{2})(\\d{4})","$1 $2",["[2-46-9]"]],[,"(5\\d{3})(\\d{4})","$1 $2",["5"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MV:[,[,,"[3467]\\d{6}|9(?:00\\d{7}|\\d{6})","\\d{7,10}",,,,,,[7,10]],[,,"(?:3(?:0[01]|3[0-59])|6(?:[567][02468]|8[024689]|90))\\d{4}",
"\\d{7}",,,"6701234",,,[7]],[,,"(?:46[46]|7[3-9]\\d|9[15-9]\\d)\\d{4}","\\d{7}",,,"7712345",,,[7]],[,,"NA","NA",,,,,,[-1]],[,,"900\\d{7}","\\d{10}",,,"9001234567",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"MV",960,"0(?:0|19)",,,,,,"00",,[[,"(\\d{3})(\\d{4})","$1-$2",["[3467]|9(?:[1-9]|0[1-9])"]],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["900"]]],,[,,"781\\d{4}","\\d{7}",,,"7812345",,,[7]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],
MW:[,[,,"(?:1(?:\\d{2})?|[2789]\\d{2})\\d{6}","\\d{7,9}",,,,,,[7,9]],[,,"(?:1[2-9]|21\\d{2})\\d{5}","\\d{7,9}",,,"1234567"],[,,"(?:111|77\\d|88\\d|99\\d)\\d{6}","\\d{9}",,,"991234567",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"MW",265,"00","0",,,"0",,,,[[,"(\\d)(\\d{3})(\\d{3})","$1 $2 $3",["1"],"0$1"],[,"(2\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["2"],"0$1"],[,"(\\d{3})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[1789]"],
"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MX:[,[,,"[1-9]\\d{9,10}","\\d{7,11}",,,,,,[10,11],[7,8]],[,,"(?:33|55|81)\\d{8}|(?:2(?:0[01]|2[2-9]|3[1-35-8]|4[13-9]|7[1-689]|8[1-578]|9[467])|3(?:1[1-79]|[2458][1-9]|7[1-8]|9[1-5])|4(?:1[1-57-9]|[24-6][1-9]|[37][1-8]|8[1-35-9]|9[2-689])|5(?:88|9[1-79])|6(?:1[2-68]|[234][1-9]|5[1-3689]|6[12457-9]|7[1-7]|8[67]|9[4-8])|7(?:[13467][1-9]|2[1-8]|5[13-9]|8[1-69]|9[17])|8(?:2[13-689]|3[1-6]|4[124-6]|6[1246-9]|7[1-378]|9[12479])|9(?:1[346-9]|2[1-4]|3[2-46-8]|5[1348]|[69][1-9]|7[12]|8[1-8]))\\d{7}",
"\\d{7,10}",,,"2221234567",,,[10]],[,,"1(?:(?:33|55|81)\\d{8}|(?:2(?:2[2-9]|3[1-35-8]|4[13-9]|7[1-689]|8[1-578]|9[467])|3(?:1[1-79]|[2458][1-9]|7[1-8]|9[1-5])|4(?:1[1-57-9]|[24-6][1-9]|[37][1-8]|8[1-35-9]|9[2-689])|5(?:88|9[1-79])|6(?:1[2-68]|[2-4][1-9]|5[1-3689]|6[12457-9]|7[1-7]|8[67]|9[4-8])|7(?:[13467][1-9]|2[1-8]|5[13-9]|8[1-69]|9[17])|8(?:2[13-689]|3[1-6]|4[124-6]|6[1246-9]|7[1-378]|9[12479])|9(?:1[346-9]|2[1-4]|3[2-46-8]|5[1348]|[69][1-9]|7[12]|8[1-8]))\\d{7})","\\d{11}",,,"12221234567",,,
[11]],[,,"8(?:00|88)\\d{7}","\\d{10}",,,"8001234567",,,[10]],[,,"900\\d{7}","\\d{10}",,,"9001234567",,,[10]],[,,"300\\d{7}","\\d{10}",,,"3001234567",,,[10]],[,,"500\\d{7}","\\d{10}",,,"5001234567",,,[10]],[,,"NA","NA",,,,,,[-1]],"MX",52,"0[09]","01",,,"0[12]|04[45](\\d{10})","1$1",,,[[,"([358]\\d)(\\d{4})(\\d{4})","$1 $2 $3",["33|55|81"],"01 $1",,1],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["[2467]|3[0-2457-9]|5[089]|8[02-9]|9[0-35-9]"],"01 $1",,1],[,"(1)([358]\\d)(\\d{4})(\\d{4})","044 $2 $3 $4",
["1(?:33|55|81)"],"$1",,1],[,"(1)(\\d{3})(\\d{3})(\\d{4})","044 $2 $3 $4",["1(?:[2467]|3[0-2457-9]|5[089]|8[2-9]|9[1-35-9])"],"$1",,1]],[[,"([358]\\d)(\\d{4})(\\d{4})","$1 $2 $3",["33|55|81"],"01 $1",,1],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["[2467]|3[0-2457-9]|5[089]|8[02-9]|9[0-35-9]"],"01 $1",,1],[,"(1)([358]\\d)(\\d{4})(\\d{4})","$1 $2 $3 $4",["1(?:33|55|81)"]],[,"(1)(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3 $4",["1(?:[2467]|3[0-2457-9]|5[089]|8[2-9]|9[1-35-9])"]]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA",
"NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],MY:[,[,,"[13-9]\\d{7,9}","\\d{6,10}",,,,,,[8,9,10],[6,7]],[,,"(?:3[2-9]\\d|[4-9][2-9])\\d{6}","\\d{6,9}",,,"323456789",,,[8,9]],[,,"1(?:1[1-5]\\d{2}|[02-4679][2-9]\\d|59\\d{2}|8(?:1[23]|[2-9]\\d))\\d{5}","\\d{9,10}",,,"123456789",,,[9,10]],[,,"1[378]00\\d{6}","\\d{10}",,,"1300123456",,,[10]],[,,"1600\\d{6}","\\d{10}",,,"1600123456",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"154\\d{7}","\\d{10}",,,"1541234567",,,[10]],
"MY",60,"00","0",,,"0",,,,[[,"([4-79])(\\d{3})(\\d{4})","$1-$2 $3",["[4-79]"],"0$1"],[,"(3)(\\d{4})(\\d{4})","$1-$2 $3",["3"],"0$1"],[,"([18]\\d)(\\d{3})(\\d{3,4})","$1-$2 $3",["1[02-46-9][1-9]|8"],"0$1"],[,"(1)([36-8]00)(\\d{2})(\\d{4})","$1-$2-$3-$4",["1[36-8]0"]],[,"(11)(\\d{4})(\\d{4})","$1-$2 $3",["11"],"0$1"],[,"(15[49])(\\d{3})(\\d{4})","$1-$2 $3",["15"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],MZ:[,[,,"[28]\\d{7,8}","\\d{8,9}",
,,,,,[8,9]],[,,"2(?:[1346]\\d|5[0-2]|[78][12]|93)\\d{5}","\\d{8}",,,"21123456",,,[8]],[,,"8[23467]\\d{7}","\\d{9}",,,"821234567",,,[9]],[,,"800\\d{6}","\\d{9}",,,"800123456",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"MZ",258,"00",,,,,,,,[[,"([28]\\d)(\\d{3})(\\d{3,4})","$1 $2 $3",["2|8[2-7]"]],[,"(80\\d)(\\d{3})(\\d{3})","$1 $2 $3",["80"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],
NA:[,[,,"[68]\\d{7,8}","\\d{8,9}",,,,,,[8,9]],[,,"6(?:1(?:17|2(?:[0189]\\d|[2-6]|7\\d?)|3(?:[01378]|2\\d)|4(?:[024]|10?|3[15]?)|69|7[014])|2(?:17|5(?:[0-36-8]|4\\d?)|69|70)|3(?:17|2(?:[0237]\\d?|[14-689])|34|6[289]|7[01]|81)|4(?:17|2(?:[012]|7\\d?)|4(?:[06]|1\\d?)|5(?:[01357]|[25]\\d?)|69|7[01])|5(?:17|2(?:[0459]|[23678]\\d?)|69|7[01])|6(?:17|2(?:5|6\\d?)|38|42|69|7[01])|7(?:17|2(?:[569]|[234]\\d?)|3(?:0\\d?|[13])|69|7[01]))\\d{4}","\\d{8,9}",,,"61221234"],[,,"(?:60|8[125])\\d{7}","\\d{9}",,,"811234567",
,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"8701\\d{5}","\\d{9}",,,"870123456",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"8(?:3\\d{2}|86)\\d{5}","\\d{8,9}",,,"88612345"],"NA",264,"00","0",,,"0",,,,[[,"(8\\d)(\\d{3})(\\d{4})","$1 $2 $3",["8[1235]"],"0$1"],[,"(6\\d)(\\d{3})(\\d{3,4})","$1 $2 $3",["6"],"0$1"],[,"(88)(\\d{3})(\\d{3})","$1 $2 $3",["88"],"0$1"],[,"(870)(\\d{3})(\\d{3})","$1 $2 $3",["870"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA",
"NA",,,,,,[-1]]],NC:[,[,,"[2-57-9]\\d{5}","\\d{6}",,,,,,[6]],[,,"(?:2[03-9]|3[0-5]|4[1-7]|88)\\d{4}","\\d{6}",,,"201234"],[,,"(?:5[0-4]|[79]\\d|8[0-79])\\d{4}","\\d{6}",,,"751234"],[,,"NA","NA",,,,,,[-1]],[,,"36\\d{4}","\\d{6}",,,"366711"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"NC",687,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})","$1.$2.$3",["[2-46-9]|5[0-4]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],NE:[,
[,,"[0289]\\d{7}","\\d{8}",,,,,,[8]],[,,"2(?:0(?:20|3[1-7]|4[134]|5[14]|6[14578]|7[1-578])|1(?:4[145]|5[14]|6[14-68]|7[169]|88))\\d{4}","\\d{8}",,,"20201234"],[,,"(?:8[089]|9\\d)\\d{6}","\\d{8}",,,"93123456"],[,,"08\\d{6}","\\d{8}",,,"08123456"],[,,"09\\d{6}","\\d{8}",,,"09123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"NE",227,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[289]|09"]],[,"(08)(\\d{3})(\\d{3})","$1 $2 $3",["08"]]],,[,,"NA","NA",
,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],NF:[,[,,"[13]\\d{5}","\\d{5,6}",,,,,,[6],[5]],[,,"(?:1(?:06|17|28|39)|3[012]\\d)\\d{3}","\\d{5,6}",,,"106609"],[,,"3[58]\\d{4}","\\d{5,6}",,,"381234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"NF",672,"00",,,,,,,,[[,"(\\d{2})(\\d{4})","$1 $2",["1"]],[,"(\\d)(\\d{5})","$1 $2",["3"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],
[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],NG:[,[,,"[1-6]\\d{5,8}|9\\d{5,9}|[78]\\d{5,13}","\\d{5,14}",,,,,,[7,8,10,11,12,13,14],[5,6]],[,,"[12]\\d{6,7}|9(?:0[3-9]|[1-9]\\d)\\d{5}|(?:3\\d|4[023568]|5[02368]|6[02-469]|7[4-69]|8[2-9])\\d{6}|(?:4[47]|5[14579]|6[1578]|7[0-357])\\d{5,6}|(?:78|41)\\d{5}","\\d{5,8}",,,"12345678",,,[7,8]],[,,"(?:1(?:7[34]\\d|8(?:04|[124579]\\d|8[0-3])|95\\d)|287[0-7]|3(?:18[1-8]|88[0-7]|9(?:8[5-9]|6[1-5]))|4(?:28[0-2]|6(?:7[1-9]|8[02-47])|88[0-2])|5(?:2(?:7[7-9]|8\\d)|38[1-79]|48[0-7]|68[4-7])|6(?:2(?:7[7-9]|8\\d)|4(?:3[7-9]|[68][129]|7[04-69]|9[1-8])|58[0-2]|98[7-9])|7(?:38[0-7]|69[1-8]|78[2-4])|8(?:28[3-9]|38[0-2]|4(?:2[12]|3[147-9]|5[346]|7[4-9]|8[014-689]|90)|58[1-8]|78[2-9]|88[5-7])|98[07]\\d)\\d{4}|(?:70[1-689]\\d|8(?:0(?:1[01]|[2-9]\\d)|1(?:[0-8]\\d|9[01]))|90[2357-9]\\d)\\d{6}",
"\\d{8,10}",,,"8021234567",,,[8,10]],[,,"800\\d{7,11}","\\d{10,14}",,,"80017591759",,,[10,11,12,13,14]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"NG",234,"009","0",,,"0",,,,[[,"(\\d)(\\d{3})(\\d{3,4})","$1 $2 $3",["[12]|9(?:0[3-9]|[1-9])"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{3,4})","$1 $2 $3",["70|8[01]|90[2357-9]"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{2,3})","$1 $2 $3",["[3-6]|7(?:[1-79]|0[1-9])|8[2-9]"],"0$1"],[,"([78]00)(\\d{4})(\\d{4,5})","$1 $2 $3",
["[78]00"],"0$1"],[,"([78]00)(\\d{5})(\\d{5,6})","$1 $2 $3",["[78]00"],"0$1"],[,"(78)(\\d{2})(\\d{3})","$1 $2 $3",["78"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"700\\d{7,11}","\\d{10,14}",,,"7001234567",,,[10,11,12,13,14]],,,[,,"NA","NA",,,,,,[-1]]],NI:[,[,,"[12578]\\d{7}","\\d{8}",,,,,,[8]],[,,"2\\d{7}","\\d{8}",,,"21234567"],[,,"5(?:5[0-7]\\d{5}|[78]\\d{6})|7[5-8]\\d{6}|8\\d{7}","\\d{8}",,,"81234567"],[,,"1800\\d{4}","\\d{8}",,,"18001234"],[,,"NA","NA",,,,,,[-1]],[,,"NA",
"NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"NI",505,"00",,,,,,,,[[,"(\\d{4})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],NL:[,[,,"1\\d{4,8}|[2-7]\\d{8}|[89]\\d{6,9}","\\d{5,10}",,,,,,[5,6,7,8,9,10]],[,,"(?:1[0135-8]|2[02-69]|3[0-68]|4[0135-9]|[57]\\d|8[478])\\d{7}","\\d{9}",,,"101234567",,,[9]],[,,"6[1-58]\\d{7}","\\d{9}",,,"612345678",,,[9]],[,,"800\\d{4,7}","\\d{7,10}",,,"8001234",,,[7,8,9,10]],[,,
"90[069]\\d{4,7}","\\d{7,10}",,,"9061234",,,[7,8,9,10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"(?:6760|85\\d{2})\\d{5}","\\d{9}",,,"851234567",,,[9]],"NL",31,"00","0",,,"0",,,,[[,"([1-578]\\d)(\\d{3})(\\d{4})","$1 $2 $3",["1[035]|2[0346]|3[03568]|4[0356]|5[0358]|7|8[4578]"],"0$1"],[,"([1-5]\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["1[16-8]|2[259]|3[124]|4[17-9]|5[124679]"],"0$1"],[,"(6)(\\d{8})","$1 $2",["6[0-57-9]"],"0$1"],[,"(66)(\\d{7})","$1 $2",["66"],"0$1"],[,"(14)(\\d{3,4})","$1 $2",
["14"],"$1"],[,"([89]0\\d)(\\d{4,7})","$1 $2",["80|9"],"0$1"]],,[,,"66\\d{7}","\\d{9}",,,"662345678",,,[9]],,,[,,"14\\d{3,4}","\\d{5,6}",,,,,,[5,6]],[,,"140(?:1(?:[035]|[16-8]\\d)|2(?:[0346]|[259]\\d)|3(?:[03568]|[124]\\d)|4(?:[0356]|[17-9]\\d)|5(?:[0358]|[124679]\\d)|7\\d|8[458])","\\d{5,6}",,,"14020",,,[5,6]],,,[,,"NA","NA",,,,,,[-1]]],NO:[,[,,"0\\d{4}|[2-9]\\d{7}","\\d{5}(?:\\d{3})?",,,,,,[5,8]],[,,"(?:2[1-4]|3[1-3578]|5[1-35-7]|6[1-4679]|7[0-8])\\d{6}","\\d{8}",,,"21234567",,,[8]],[,,"(?:4[015-8]|5[89]|87|9\\d)\\d{6}",
"\\d{8}",,,"40612345",,,[8]],[,,"80[01]\\d{5}","\\d{8}",,,"80012345",,,[8]],[,,"82[09]\\d{5}","\\d{8}",,,"82012345",,,[8]],[,,"810(?:0[0-6]|[2-8]\\d)\\d{3}","\\d{8}",,,"81021234",,,[8]],[,,"880\\d{5}","\\d{8}",,,"88012345",,,[8]],[,,"85[0-5]\\d{5}","\\d{8}",,,"85012345",,,[8]],"NO",47,"00",,,,,,,,[[,"([489]\\d{2})(\\d{2})(\\d{3})","$1 $2 $3",["[489]"]],[,"([235-7]\\d)(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[235-7]"]]],,[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]],[,,"0\\d{4}|81(?:0(?:0[7-9]|1\\d)|5\\d{2})\\d{3}",
"\\d{5}(?:\\d{3})?",,,"01234"],1,,[,,"81[23]\\d{5}","\\d{8}",,,"81212345",,,[8]]],NP:[,[,,"[1-8]\\d{7}|9(?:[1-69]\\d{6,8}|7[2-6]\\d{5,7}|8\\d{8})","\\d{6,10}",,,,,,[8,10],[6,7]],[,,"(?:1[0-6]\\d|2[13-79][2-6]|3[135-8][2-6]|4[146-9][2-6]|5[135-7][2-6]|6[13-9][2-6]|7[15-9][2-6]|8[1-46-9][2-6]|9[1-79][2-6])\\d{5}","\\d{6,8}",,,"14567890",,,[8]],[,,"9(?:6[013]|7[245]|8[0-24-6])\\d{7}","\\d{10}",,,"9841234567",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",
,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"NP",977,"00","0",,,"0",,,,[[,"(1)(\\d{7})","$1-$2",["1[2-6]"],"0$1"],[,"(\\d{2})(\\d{6})","$1-$2",["1[01]|[2-8]|9(?:[1-69]|7[15-9])"],"0$1"],[,"(9\\d{2})(\\d{7})","$1-$2",["9(?:6[013]|7[245]|8)"],"$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],NR:[,[,,"[458]\\d{6}","\\d{7}",,,,,,[7]],[,,"(?:444|888)\\d{4}","\\d{7}",,,"4441234"],[,,"55[5-9]\\d{4}","\\d{7}",,,"5551234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",
,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"NR",674,"00",,,,,,,,[[,"(\\d{3})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],NU:[,[,,"[1-5]\\d{3}","\\d{4}",,,,,,[4]],[,,"[34]\\d{3}","\\d{4}",,,"4002"],[,,"[125]\\d{3}","\\d{4}",,,"1234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"NU",683,"00",,,,,,,,,,[,,"NA",
"NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],NZ:[,[,,"6[235-9]\\d{6}|[2-57-9]\\d{7,10}","\\d{7,11}",,,,,,[8,9,10,11],[7]],[,,"(?:3[2-79]|[49][2-9]|6[235-9]|7[2-57-9])\\d{6}|24099\\d{3}","\\d{7,8}",,,"32345678",,,[8]],[,,"2(?:[028]\\d{7,8}|1(?:[03]\\d{5,7}|[12457]\\d{5,6}|[689]\\d{5})|[79]\\d{7})","\\d{8,10}",,,"211234567",,,[8,9,10]],[,,"508\\d{6,7}|80\\d{6,8}","\\d{8,10}",,,"800123456",,,[8,9,10]],[,,"90\\d{7,9}","\\d{9,11}",,,"900123456",,,[9,10,11]],
[,,"NA","NA",,,,,,[-1]],[,,"70\\d{7}","\\d{9}",,,"701234567",,,[9]],[,,"NA","NA",,,,,,[-1]],"NZ",64,"0(?:0|161)","0",,,"0",,"00",,[[,"([34679])(\\d{3})(\\d{4})","$1-$2 $3",["[346]|7[2-57-9]|9[1-9]"],"0$1"],[,"(24099)(\\d{3})","$1 $2",["240","2409","24099"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["21"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{3,5})","$1 $2 $3",["2(?:1[1-9]|[69]|7[0-35-9])|70|86"],"0$1"],[,"(2\\d)(\\d{3,4})(\\d{4})","$1 $2 $3",["2[028]"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{3,4})","$1 $2 $3",
["2(?:10|74)|5|[89]0"],"0$1"]],,[,,"[28]6\\d{6,7}","\\d{8,9}",,,"26123456",,,[8,9]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],OM:[,[,,"(?:5|[279]\\d)\\d{6}|800\\d{5,6}","\\d{7,9}",,,,,,[7,8,9]],[,,"2[2-6]\\d{6}","\\d{8}",,,"23123456",,,[8]],[,,"7[19]\\d{6}|9(?:0[1-9]|[1-9]\\d)\\d{5}","\\d{8}",,,"92123456",,,[8]],[,,"8007\\d{4,5}|500\\d{4}","\\d{7,9}",,,"80071234"],[,,"900\\d{5}","\\d{8}",,,"90012345",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA",
"NA",,,,,,[-1]],"OM",968,"00",,,,,,,,[[,"(2\\d)(\\d{6})","$1 $2",["2"]],[,"([79]\\d{3})(\\d{4})","$1 $2",["[79]"]],[,"([58]00)(\\d{4,6})","$1 $2",["[58]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],PA:[,[,,"[1-9]\\d{6,7}","\\d{7,8}",,,,,,[7,8]],[,,"(?:1(?:0[0-8]|1[49]|2[37]|3[0137]|4[147]|5[05]|6[58]|7[0167]|8[58]|9[139])|2(?:[0235679]\\d|1[0-7]|4[04-9]|8[028])|3(?:[09]\\d|1[014-7]|2[0-3]|3[03]|4[03-57]|55|6[068]|7[06-8]|8[06-9])|4(?:3[013-69]|4\\d|7[0-589])|5(?:[01]\\d|2[0-7]|[56]0|79)|7(?:0[09]|2[0-267]|3[06]|[469]0|5[06-9]|7[0-24-79]|8[7-9])|8(?:09|[34]\\d|5[0134]|8[02])|9(?:0[6-9]|1[016-8]|2[036-8]|3[3679]|40|5[0489]|6[06-9]|7[046-9]|8[36-8]|9[1-9]))\\d{4}",
"\\d{7}",,,"2001234",,,[7]],[,,"(?:1[16]1|21[89]|8(?:1[01]|7[23]))\\d{4}|6(?:[024-9]\\d|1[0-5]|3[0-24-9])\\d{5}","\\d{7,8}",,,"60012345"],[,,"80[09]\\d{4}","\\d{7}",,,"8001234",,,[7]],[,,"(?:779|8(?:55|60|7[78])|9(?:00|81))\\d{4}","\\d{7}",,,"8601234",,,[7]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"PA",507,"00",,,,,,,,[[,"(\\d{3})(\\d{4})","$1-$2",["[1-57-9]"]],[,"(\\d{4})(\\d{4})","$1-$2",["6"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,
,[-1]],,,[,,"NA","NA",,,,,,[-1]]],PE:[,[,,"[14-9]\\d{7,8}","\\d{6,9}",,,,,,[8,9],[6,7]],[,,"(?:1\\d|4[1-4]|5[1-46]|6[1-7]|7[2-46]|8[2-4])\\d{6}","\\d{6,8}",,,"11234567",,,[8]],[,,"9\\d{8}","\\d{9}",,,"912345678",,,[9]],[,,"800\\d{5}","\\d{8}",,,"80012345",,,[8]],[,,"805\\d{5}","\\d{8}",,,"80512345",,,[8]],[,,"801\\d{5}","\\d{8}",,,"80112345",,,[8]],[,,"80[24]\\d{5}","\\d{8}",,,"80212345",,,[8]],[,,"NA","NA",,,,,,[-1]],"PE",51,"19(?:1[124]|77|90)00","0"," Anexo ",,"0",,,,[[,"(1)(\\d{7})","$1 $2",["1"],
"(0$1)"],[,"([4-8]\\d)(\\d{6})","$1 $2",["[4-7]|8[2-4]"],"(0$1)"],[,"(\\d{3})(\\d{5})","$1 $2",["80"],"(0$1)"],[,"(9\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["9"],"$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],PF:[,[,,"4\\d{5,7}|8\\d{7}","\\d{6}(?:\\d{2})?",,,,,,[6,8]],[,,"4(?:[09][45689]\\d|4)\\d{4}","\\d{6}(?:\\d{2})?",,,"40412345"],[,,"8[79]\\d{6}","\\d{8}",,,"87123456",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",
,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"PF",689,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["4[09]|8[79]"]],[,"(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3",["44"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"44\\d{4}","\\d{6}",,,"441234",,,[6]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],PG:[,[,,"[1-9]\\d{6,7}","\\d{7,8}",,,,,,[7,8]],[,,"(?:3[0-2]\\d|4[25]\\d|5[34]\\d|64[1-9]|77(?:[0-24]\\d|30)|85[02-46-9]|9[78]\\d)\\d{4}","\\d{7}",,,"3123456",,,[7]],[,,"(?:20150|68\\d{2}|7(?:[0-689]\\d|75)\\d{2})\\d{3}",
"\\d{7,8}",,,"6812345"],[,,"180\\d{4}","\\d{7}",,,"1801234",,,[7]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"275\\d{4}","\\d{7}",,,"2751234",,,[7]],"PG",675,"00",,,,,,,,[[,"(\\d{3})(\\d{4})","$1 $2",["[13-689]|27"]],[,"(\\d{4})(\\d{4})","$1 $2",["20|7"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],PH:[,[,,"2\\d{5,7}|[3-9]\\d{7,9}|1800\\d{7,9}","\\d{5,13}",,,,,,[6,8,9,10,11,12,13],[5,7]],[,,"2\\d{5}(?:\\d{2})?|(?:3[2-68]|4[2-9]|5[2-6]|6[2-58]|7[24578]|8[2-8])\\d{7}|88(?:22\\d{6}|42\\d{4})",
"\\d{5,10}",,,"21234567",,,[6,8,9,10]],[,,"(?:81[37]|9(?:0[5-9]|1[024-9]|2[0-35-9]|3[02-9]|4[236-9]|50|7[34-79]|89|9[4-9]))\\d{7}","\\d{10}",,,"9051234567",,,[10]],[,,"1800\\d{7,9}","\\d{11,13}",,,"180012345678",,,[11,12,13]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"PH",63,"00","0",,,"0",,,,[[,"(2)(\\d{3})(\\d{4})","$1 $2 $3",["2"],"(0$1)"],[,"(2)(\\d{5})","$1 $2",["2"],"(0$1)"],[,"(\\d{4})(\\d{4,6})","$1 $2",["3(?:23|39|46)|4(?:2[3-6]|[35]9|4[26]|76)|5(?:22|44)|642|8(?:62|8[245])",
"3(?:230|397|461)|4(?:2(?:35|[46]4|51)|396|4(?:22|63)|59[347]|76[15])|5(?:221|446)|642[23]|8(?:622|8(?:[24]2|5[13]))"],"(0$1)"],[,"(\\d{5})(\\d{4})","$1 $2",["346|4(?:27|9[35])|883","3469|4(?:279|9(?:30|56))|8834"],"(0$1)"],[,"([3-8]\\d)(\\d{3})(\\d{4})","$1 $2 $3",["[3-8]"],"(0$1)"],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["81|9"],"0$1"],[,"(1800)(\\d{3})(\\d{4})","$1 $2 $3",["1"]],[,"(1800)(\\d{1,2})(\\d{3})(\\d{4})","$1 $2 $3 $4",["1"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA",
"NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],PK:[,[,,"1\\d{8}|[2-8]\\d{5,11}|9(?:[013-9]\\d{4,9}|2\\d(?:111\\d{6}|\\d{3,7}))","\\d{6,12}",,,,,,[8,9,10,11,12],[6,7]],[,,"(?:21|42)[2-9]\\d{7}|(?:2[25]|4[0146-9]|5[1-35-7]|6[1-8]|7[14]|8[16]|91)[2-9]\\d{6}|(?:2(?:3[2358]|4[2-4]|9[2-8])|45[3479]|54[2-467]|60[468]|72[236]|8(?:2[2-689]|3[23578]|4[3478]|5[2356])|9(?:2[2-8]|3[27-9]|4[2-6]|6[3569]|9[25-8]))[2-9]\\d{5,6}|58[126]\\d{7}","\\d{6,10}",,,"2123456789",,,[9,10]],[,,"3(?:0\\d|1[0-6]|2[0-5]|3[0-7]|4[0-8]|55|64)\\d{7}",
"\\d{10}",,,"3012345678",,,[10]],[,,"800\\d{5}","\\d{8}",,,"80012345",,,[8]],[,,"900\\d{5}","\\d{8}",,,"90012345",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"122\\d{6}","\\d{9}",,,"122044444",,,[9]],[,,"NA","NA",,,,,,[-1]],"PK",92,"00","0",,,"0",,,,[[,"(\\d{2})(111)(\\d{3})(\\d{3})","$1 $2 $3 $4",["(?:2[125]|4[0-246-9]|5[1-35-7]|6[1-8]|7[14]|8[16]|91)1","(?:2[125]|4[0-246-9]|5[1-35-7]|6[1-8]|7[14]|8[16]|91)11","(?:2[125]|4[0-246-9]|5[1-35-7]|6[1-8]|7[14]|8[16]|91)111"],"(0$1)"],[,"(\\d{3})(111)(\\d{3})(\\d{3})",
"$1 $2 $3 $4",["2[349]|45|54|60|72|8[2-5]|9[2-9]","(?:2[349]|45|54|60|72|8[2-5]|9[2-9])\\d1","(?:2[349]|45|54|60|72|8[2-5]|9[2-9])\\d11","(?:2[349]|45|54|60|72|8[2-5]|9[2-9])\\d111"],"(0$1)"],[,"(\\d{2})(\\d{7,8})","$1 $2",["(?:2[125]|4[0-246-9]|5[1-35-7]|6[1-8]|7[14]|8[16]|91)[2-9]"],"(0$1)"],[,"(\\d{3})(\\d{6,7})","$1 $2",["2[349]|45|54|60|72|8[2-5]|9[2-9]","(?:2[349]|45|54|60|72|8[2-5]|9[2-9])\\d[2-9]"],"(0$1)"],[,"(3\\d{2})(\\d{7})","$1 $2",["3"],"0$1"],[,"([15]\\d{3})(\\d{5,6})","$1 $2",["58[12]|1"],
"(0$1)"],[,"(586\\d{2})(\\d{5})","$1 $2",["586"],"(0$1)"],[,"([89]00)(\\d{3})(\\d{2})","$1 $2 $3",["[89]00"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"(?:2(?:[125]|3[2358]|4[2-4]|9[2-8])|4(?:[0-246-9]|5[3479])|5(?:[1-35-7]|4[2-467])|6(?:[1-8]|0[468])|7(?:[14]|2[236])|8(?:[16]|2[2-689]|3[23578]|4[3478]|5[2356])|9(?:1|22|3[27-9]|4[2-6]|6[3569]|9[2-7]))111\\d{6}","\\d{11,12}",,,"21111825888",,,[11,12]],,,[,,"NA","NA",,,,,,[-1]]],PL:[,[,,"[12]\\d{6,8}|[3-57-9]\\d{8}|6\\d{5,8}","\\d{6,9}",
,,,,,[6,7,8,9]],[,,"(?:1[2-8]|2[2-69]|3[2-4]|4[1-468]|5[24-689]|6[1-3578]|7[14-7]|8[1-79]|9[145])\\d{7}|[12]2\\d{5}","\\d{6,9}",,,"123456789",,,[7,9]],[,,"(?:5[0137]|6[069]|7[2389]|88)\\d{7}","\\d{9}",,,"512345678",,,[9]],[,,"800\\d{6}","\\d{9}",,,"800123456",,,[9]],[,,"70\\d{7}","\\d{9}",,,"701234567",,,[9]],[,,"801\\d{6}","\\d{9}",,,"801234567",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"39\\d{7}","\\d{9}",,,"391234567",,,[9]],"PL",48,"00",,,,,,,,[[,"(\\d{2})(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[14]|2[0-57-9]|3[2-4]|5[24-689]|6[1-3578]|7[14-7]|8[1-79]|9[145]"]],
[,"(\\d{2})(\\d{1})(\\d{4})","$1 $2 $3",["[12]2"]],[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3",["26|39|5[0137]|6[0469]|7[02389]|8[08]"]],[,"(\\d{3})(\\d{2})(\\d{2,3})","$1 $2 $3",["64"]],[,"(\\d{3})(\\d{3})","$1 $2",["64"]]],,[,,"64\\d{4,7}","\\d{6,9}",,,"641234567"],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],PM:[,[,,"[45]\\d{5}","\\d{6}",,,,,,[6]],[,,"41\\d{4}","\\d{6}",,,"411234"],[,,"55\\d{4}","\\d{6}",,,"551234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,
,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"PM",508,"00","0",,,"0",,,,[[,"([45]\\d)(\\d{2})(\\d{2})","$1 $2 $3",,"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],PR:[,[,,"[5789]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"(?:787|939)[2-9]\\d{6}","\\d{7}(?:\\d{3})?",,,"7872345678"],[,,"(?:787|939)[2-9]\\d{6}","\\d{7}(?:\\d{3})?",,,"7872345678"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002345678"],
[,,"900[2-9]\\d{6}","\\d{10}",,,"9002345678"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"PR",1,"011","1",,,"1",,,1,,,[,,"NA","NA",,,,,,[-1]],,"787|939",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],PS:[,[,,"[24589]\\d{7,8}|1(?:[78]\\d{8}|[49]\\d{2,3})","\\d{4,10}",,,,,,[4,5,8,9,10],[7]],[,,"(?:22[234789]|42[45]|82[01458]|92[369])\\d{5}","\\d{7,8}",,,"22234567",,,[8]],[,,"5[69]\\d{7}","\\d{9}",,,
"599123456",,,[9]],[,,"1800\\d{6}","\\d{10}",,,"1800123456",,,[10]],[,,"1(?:4|9\\d)\\d{2}","\\d{4,5}",,,"19123",,,[4,5]],[,,"1700\\d{6}","\\d{10}",,,"1700123456",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"PS",970,"00","0",,,"0",,,,[[,"([2489])(2\\d{2})(\\d{4})","$1 $2 $3",["[2489]"],"0$1"],[,"(5[69]\\d)(\\d{3})(\\d{3})","$1 $2 $3",["5"],"0$1"],[,"(1[78]00)(\\d{3})(\\d{3})","$1 $2 $3",["1[78]"],"$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA",
"NA",,,,,,[-1]]],PT:[,[,,"[2-46-9]\\d{8}","\\d{9}",,,,,,[9]],[,,"2(?:[12]\\d|[35][1-689]|4[1-59]|6[1-35689]|7[1-9]|8[1-69]|9[1256])\\d{6}","\\d{9}",,,"212345678"],[,,"9(?:[1236]\\d{2}|480)\\d{5}","\\d{9}",,,"912345678"],[,,"80[02]\\d{6}","\\d{9}",,,"800123456"],[,,"6(?:0[178]|4[68])\\d{6}|76(?:0[1-57]|1[2-47]|2[237])\\d{5}","\\d{9}",,,"760123456"],[,,"80(?:8\\d|9[1579])\\d{5}","\\d{9}",,,"808123456"],[,,"884[0-4689]\\d{5}","\\d{9}",,,"884123456"],[,,"30\\d{7}","\\d{9}",,,"301234567"],"PT",351,"00",
,,,,,,,[[,"(2\\d)(\\d{3})(\\d{4})","$1 $2 $3",["2[12]"]],[,"([2-46-9]\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["2[3-9]|[346-9]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"7(?:0(?:7\\d|8[17]))\\d{5}","\\d{9}",,,"707123456"],,,[,,"600\\d{6}","\\d{9}",,,"600110000"]],PW:[,[,,"[2-8]\\d{6}","\\d{7}",,,,,,[7]],[,,"2552255|(?:277|345|488|5(?:35|44|87)|6(?:22|54|79)|7(?:33|47)|8(?:24|55|76))\\d{4}","\\d{7}",,,"2771234"],[,,"(?:6[234689]0|77[45789])\\d{4}","\\d{7}",,,"6201234"],[,,"NA","NA",,,,,
,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"PW",680,"01[12]",,,,,,,,[[,"(\\d{3})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],PY:[,[,,"5[0-5]\\d{4,7}|[2-46-9]\\d{5,8}","\\d{5,9}",,,,,,[6,7,8,9],[5]],[,,"(?:[26]1|3[289]|4[124678]|7[123]|8[1236])\\d{5,7}|(?:2(?:2[4568]|7[15]|9[1-5])|3(?:18|3[167]|4[2357]|51)|4(?:18|2[45]|3[12]|5[13]|64|71|9[1-47])|5(?:[1-4]\\d|5[0234])|6(?:3[1-3]|44|7[1-4678])|7(?:17|4[0-4]|6[1-578]|75|8[0-8])|858)\\d{5,6}",
"\\d{5,9}",,,"212345678",,,[7,8,9]],[,,"9(?:6[12]|[78][1-6]|9[1-5])\\d{6}","\\d{9}",,,"961456789",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"8700[0-4]\\d{4}","\\d{9}",,,"870012345",,,[9]],"PY",595,"00","0",,,"0",,,,[[,"(\\d{2})(\\d{5})","$1 $2",["(?:[26]1|3[289]|4[124678]|7[123]|8[1236])"],"(0$1)"],[,"(\\d{2})(\\d{3})(\\d{3,4})","$1 $2 $3",["(?:[26]1|3[289]|4[124678]|7[123]|8[1236])"],"(0$1)"],[,"(\\d{3})(\\d{3,6})","$1 $2",["[2-9]0"],
"0$1"],[,"(\\d{3})(\\d{6})","$1 $2",["9[1-9]"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["8700"]],[,"(\\d{3})(\\d{4,5})","$1 $2",["[2-8][1-9]"],"(0$1)"],[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3",["[2-8][1-9]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"[2-9]0\\d{4,7}","\\d{6,9}",,,"201234567"],,,[,,"NA","NA",,,,,,[-1]]],QA:[,[,,"[2-8]\\d{6,7}","\\d{7,8}",,,,,,[7,8]],[,,"4[04]\\d{6}","\\d{8}",,,"44123456",,,[8]],[,,"[3567]\\d{7}","\\d{8}",,,"33123456",,,[8]],[,,"800\\d{4}",
"\\d{7}",,,"8001234",,,[7]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"QA",974,"00",,,,,,,,[[,"([28]\\d{2})(\\d{4})","$1 $2",["[28]"]],[,"([3-7]\\d{3})(\\d{4})","$1 $2",["[3-7]"]]],,[,,"2(?:[12]\\d|61)\\d{4}","\\d{7}",,,"2123456",,,[7]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],RE:[,[,,"[268]\\d{8}","\\d{9}",,,,,,[9]],[,,"262\\d{6}","\\d{9}",,,"262161234"],[,,"6(?:9[23]|47)\\d{6}","\\d{9}",,,"692123456"],[,,"80\\d{7}",
"\\d{9}",,,"801234567"],[,,"89[1-37-9]\\d{6}","\\d{9}",,,"891123456"],[,,"8(?:1[019]|2[0156]|84|90)\\d{6}","\\d{9}",,,"810123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"RE",262,"00","0",,,"0",,,,[[,"([268]\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",,"0$1"]],,[,,"NA","NA",,,,,,[-1]],1,"262|6[49]|8",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],RO:[,[,,"2\\d{5,8}|[37-9]\\d{8}","\\d{6,9}",,,,,,[6,9]],[,,"2(?:1(?:\\d{7}|9\\d{3})|[3-6](?:\\d{7}|\\d9\\d{2}))|3[13-6]\\d{7}",
"\\d{6,9}",,,"211234567"],[,,"7(?:[0-8]\\d{2}|99\\d)\\d{5}","\\d{9}",,,"712345678",,,[9]],[,,"800\\d{6}","\\d{9}",,,"800123456",,,[9]],[,,"90[036]\\d{6}","\\d{9}",,,"900123456",,,[9]],[,,"801\\d{6}","\\d{9}",,,"801123456",,,[9]],[,,"802\\d{6}","\\d{9}",,,"802123456",,,[9]],[,,"NA","NA",,,,,,[-1]],"RO",40,"00","0"," int ",,"0",,,,[[,"(\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["[23]1"],"0$1"],[,"(21)(\\d{4})","$1 $2",["21"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3",["[23][3-7]|[7-9]"],"0$1"],[,"(2\\d{2})(\\d{3})",
"$1 $2",["2[3-6]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"37\\d{7}","\\d{9}",,,"372123456",,,[9]],,,[,,"NA","NA",,,,,,[-1]]],RS:[,[,,"[126-9]\\d{4,11}|3(?:[0-79]\\d{3,10}|8[2-9]\\d{2,9})","\\d{5,12}",,,,,,[6,7,8,9,10,11,12],[5]],[,,"(?:1(?:[02-9][2-9]|1[1-9])\\d|2(?:[0-24-7][2-9]\\d|[389](?:0[2-9]|[2-9]\\d))|3(?:[0-8][2-9]\\d|9(?:[2-9]\\d|0[2-9])))\\d{3,8}","\\d{5,12}",,,"10234567",,,[7,8,9,10,11,12]],[,,"6(?:[0-689]|7\\d)\\d{6,7}","\\d{8,10}",,,"601234567",,,[8,9,10]],[,,
"800\\d{3,9}","\\d{6,12}",,,"80012345"],[,,"(?:90[0169]|78\\d)\\d{3,7}","\\d{6,12}",,,"90012345"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"RS",381,"00","0",,,"0",,,,[[,"([23]\\d{2})(\\d{4,9})","$1 $2",["(?:2[389]|39)0"],"0$1"],[,"([1-3]\\d)(\\d{5,10})","$1 $2",["1|2(?:[0-24-7]|[389][1-9])|3(?:[0-8]|9[1-9])"],"0$1"],[,"(6\\d)(\\d{6,8})","$1 $2",["6"],"0$1"],[,"([89]\\d{2})(\\d{3,9})","$1 $2",["[89]"],"0$1"],[,"(7[26])(\\d{4,9})","$1 $2",["7[26]"],"0$1"],[,"(7[08]\\d)(\\d{4,9})",
"$1 $2",["7[08]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"7[06]\\d{4,10}","\\d{6,12}",,,"700123456"],,,[,,"NA","NA",,,,,,[-1]]],RU:[,[,,"[3489]\\d{9}","\\d{10}",,,,,,[10]],[,,"(?:3(?:0[12]|4[1-35-79]|5[1-3]|65|8[1-58]|9[0145])|4(?:01|1[1356]|2[13467]|7[1-5]|8[1-7]|9[1-689])|8(?:1[1-8]|2[01]|3[13-6]|4[0-8]|5[15]|6[1-35-79]|7[1-37-9]))\\d{7}","\\d{10}",,,"3011234567"],[,,"9\\d{9}","\\d{10}",,,"9123456789"],[,,"80[04]\\d{7}","\\d{10}",,,"8001234567"],[,,"80[39]\\d{7}","\\d{10}",
,,"8091234567"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"RU",7,"810","8",,,"8",,"8~10",,[[,"(\\d{3})(\\d{2})(\\d{2})","$1-$2-$3",["[1-79]"],"$1",,1],[,"([3489]\\d{2})(\\d{3})(\\d{2})(\\d{2})","$1 $2-$3-$4",["[34689]"],"8 ($1)",,1],[,"(7\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["7"],"8 ($1)",,1]],[[,"([3489]\\d{2})(\\d{3})(\\d{2})(\\d{2})","$1 $2-$3-$4",["[34689]"],"8 ($1)",,1],[,"(7\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["7"],"8 ($1)",,1]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA",
"NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],RW:[,[,,"[027-9]\\d{7,8}","\\d{8,9}",,,,,,[8,9]],[,,"2[258]\\d{7}|06\\d{6}","\\d{8,9}",,,"250123456"],[,,"7[238]\\d{7}","\\d{9}",,,"720123456",,,[9]],[,,"800\\d{6}","\\d{9}",,,"800123456",,,[9]],[,,"900\\d{6}","\\d{9}",,,"900123456",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"RW",250,"00","0",,,"0",,,,[[,"(2\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["2"],"$1"],[,"([7-9]\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",
["[7-9]"],"0$1"],[,"(0\\d)(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["0"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],SA:[,[,,"1\\d{7,8}|(?:[2-467]|92)\\d{7}|5\\d{8}|8\\d{9}","\\d{7,10}",,,,,,[8,9,10],[7]],[,,"11\\d{7}|1?(?:2[24-8]|3[35-8]|4[3-68]|6[2-5]|7[235-7])\\d{6}","\\d{7,9}",,,"112345678",,,[8,9]],[,,"(?:5(?:[013-689]\\d|7[0-26-8])|811\\d)\\d{6}","\\d{9,10}",,,"512345678",,,[9,10]],[,,"800\\d{7}","\\d{10}",,,"8001234567",,,[10]],[,
,"NA","NA",,,,,,[-1]],[,,"92[05]\\d{6}","\\d{9}",,,"920012345",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"SA",966,"00","0",,,"0",,,,[[,"([1-467])(\\d{3})(\\d{4})","$1 $2 $3",["[1-467]"],"0$1"],[,"(1\\d)(\\d{3})(\\d{4})","$1 $2 $3",["1[1-467]"],"0$1"],[,"(5\\d)(\\d{3})(\\d{4})","$1 $2 $3",["5"],"0$1"],[,"(92\\d{2})(\\d{5})","$1 $2",["92"],"$1"],[,"(800)(\\d{3})(\\d{4})","$1 $2 $3",["80"],"$1"],[,"(811)(\\d{3})(\\d{3,4})","$1 $2 $3",["81"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",
,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],SB:[,[,,"[1-9]\\d{4,6}","\\d{5,7}",,,,,,[5,7]],[,,"(?:1[4-79]|[23]\\d|4[0-2]|5[03]|6[0-37])\\d{3}","\\d{5}",,,"40123",,,[5]],[,,"48\\d{3}|7(?:30|[46-8]\\d|5[025-9]|9[0-5])\\d{4}|8[4-9]\\d{5}|9(?:1[2-9]|2[013-9]|3[0-2]|[46]\\d|5[0-46-9]|7[0-689]|8[0-79]|9[0-8])\\d{4}","\\d{5,7}",,,"7421234"],[,,"1[38]\\d{3}","\\d{5}",,,"18123",,,[5]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"5[12]\\d{3}","\\d{5}",,,"51123",
,,[5]],"SB",677,"0[01]",,,,,,,,[[,"(\\d{2})(\\d{5})","$1 $2",["[7-9]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],SC:[,[,,"[2468]\\d{5,6}","\\d{6,7}",,,,,,[6,7]],[,,"4[2-46]\\d{5}","\\d{7}",,,"4217123",,,[7]],[,,"2[5-8]\\d{5}","\\d{7}",,,"2510123",,,[7]],[,,"8000\\d{2}","\\d{6}",,,"800000",,,[6]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"64\\d{5}","\\d{7}",,,"6412345",,,[7]],"SC",248,"0[0-2]",,,,,,"00",
,[[,"(\\d{3})(\\d{3})","$1 $2",["8"]],[,"(\\d)(\\d{3})(\\d{3})","$1 $2 $3",["[246]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],SD:[,[,,"[19]\\d{8}","\\d{9}",,,,,,[9]],[,,"1(?:[125]\\d|8[3567])\\d{6}","\\d{9}",,,"121231234"],[,,"9[0-3569]\\d{7}","\\d{9}",,,"911231234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"SD",249,"00","0",,,"0",,,,[[,"(\\d{2})(\\d{3})(\\d{4})",
"$1 $2 $3",,"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],SE:[,[,,"[1-35-9]\\d{5,11}|4\\d{6,8}","\\d{6,12}",,,,,,[6,7,8,9,10,12]],[,,"1(?:0[1-8]\\d{6}|[136]\\d{5,7}|(?:2[0-35]|4[0-4]|5[0-25-9]|7[13-6]|[89]\\d)\\d{5,6})|2(?:[136]\\d{5,7}|(?:2[0-7]|4[0136-8]|5[0138]|7[018]|8[01]|9[0-57])\\d{5,6})|3(?:[356]\\d{5,7}|(?:0[0-4]|1\\d|2[0-25]|4[056]|7[0-2]|8[0-3]|9[023])\\d{5,6})|4(?:[0246]\\d{5,7}|(?:1[013-8]|3[0135]|5[14-79]|7[0-246-9]|8[0156]|9[0-689])\\d{5,6})|5(?:0[0-6]|[15][0-5]|2[0-68]|3[0-4]|4\\d|6[03-5]|7[013]|8[0-79]|9[01])\\d{5,6}|6(?:[03]\\d{5,7}|(?:1[1-3]|2[0-4]|4[02-57]|5[0-37]|6[0-3]|7[0-2]|8[0247]|9[0-356])\\d{5,6})|8\\d{6,8}|9(?:0[1-9]\\d{4,6}|(?:1[0-68]|2\\d|3[02-5]|4[0-3]|5[0-4]|[68][01]|7[0135-8])\\d{5,6})",
"\\d{7,9}",,,"8123456",,,[7,8,9]],[,,"7[02369]\\d{7}","\\d{9}",,,"701234567",,,[9]],[,,"20\\d{4,7}","\\d{6,9}",,,"20123456",,,[6,7,8,9]],[,,"649\\d{6}|9(?:00|39|44)[1-8]\\d{3,6}","\\d{7,10}",,,"9001234567",,,[7,8,9,10]],[,,"77(?:0\\d{3}(?:\\d{3})?|[1-7]\\d{6})","\\d{6}(?:\\d{3})?",,,"771234567",,,[6,9]],[,,"75[1-8]\\d{6}","\\d{9}",,,"751234567",,,[9]],[,,"NA","NA",,,,,,[-1]],"SE",46,"00","0",,,"0",,,,[[,"(8)(\\d{2,3})(\\d{2,3})(\\d{2})","$1-$2 $3 $4",["8"],"0$1"],[,"([1-69]\\d)(\\d{2,3})(\\d{2})(\\d{2})",
"$1-$2 $3 $4",["1[013689]|2[0136]|3[1356]|4[0246]|54|6[03]|90"],"0$1"],[,"([1-469]\\d)(\\d{3})(\\d{2})","$1-$2 $3",["1[136]|2[136]|3[356]|4[0246]|6[03]|90"],"0$1"],[,"(\\d{3})(\\d{2})(\\d{2})(\\d{2})","$1-$2 $3 $4",["1[2457]|2(?:[247-9]|5[0138])|3[0247-9]|4[1357-9]|5[0-35-9]|6(?:[124-689]|7[0-2])|9(?:[125-8]|3[0-5]|4[0-3])"],"0$1"],[,"(\\d{3})(\\d{2,3})(\\d{2})","$1-$2 $3",["1[2457]|2(?:[247-9]|5[0138])|3[0247-9]|4[1357-9]|5[0-35-9]|6(?:[124-689]|7[0-2])|9(?:[125-8]|3[0-5]|4[0-3])"],"0$1"],[,"(7\\d)(\\d{3})(\\d{2})(\\d{2})",
"$1-$2 $3 $4",["7"],"0$1"],[,"(77)(\\d{2})(\\d{2})","$1-$2$3",["7"],"0$1"],[,"(20)(\\d{2,3})(\\d{2})","$1-$2 $3",["20"],"0$1"],[,"(9[034]\\d)(\\d{2})(\\d{2})(\\d{3})","$1-$2 $3 $4",["9[034]"],"0$1"],[,"(9[034]\\d)(\\d{4})","$1-$2",["9[034]"],"0$1"],[,"(\\d{3})(\\d{2})(\\d{3})(\\d{2})(\\d{2})","$1-$2 $3 $4 $5",["25[245]|67[3-6]"],"0$1"]],[[,"(8)(\\d{2,3})(\\d{2,3})(\\d{2})","$1 $2 $3 $4",["8"]],[,"([1-69]\\d)(\\d{2,3})(\\d{2})(\\d{2})","$1 $2 $3 $4",["1[013689]|2[0136]|3[1356]|4[0246]|54|6[03]|90"]],
[,"([1-469]\\d)(\\d{3})(\\d{2})","$1 $2 $3",["1[136]|2[136]|3[356]|4[0246]|6[03]|90"]],[,"(\\d{3})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["1[2457]|2(?:[247-9]|5[0138])|3[0247-9]|4[1357-9]|5[0-35-9]|6(?:[124-689]|7[0-2])|9(?:[125-8]|3[0-5]|4[0-3])"]],[,"(\\d{3})(\\d{2,3})(\\d{2})","$1 $2 $3",["1[2457]|2(?:[247-9]|5[0138])|3[0247-9]|4[1357-9]|5[0-35-9]|6(?:[124-689]|7[0-2])|9(?:[125-8]|3[0-5]|4[0-3])"]],[,"(7\\d)(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3 $4",["7"]],[,"(77)(\\d{2})(\\d{2})","$1 $2 $3",["7"]],
[,"(20)(\\d{2,3})(\\d{2})","$1 $2 $3",["20"]],[,"(9[034]\\d)(\\d{2})(\\d{2})(\\d{3})","$1 $2 $3 $4",["9[034]"]],[,"(9[034]\\d)(\\d{4})","$1 $2",["9[034]"]],[,"(\\d{3})(\\d{2})(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3 $4 $5",["25[245]|67[3-6]"]]],[,,"74[02-9]\\d{6}","\\d{9}",,,"740123456",,,[9]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"(?:25[245]|67[3-6])\\d{9}","\\d{12}",,,"254123456789",,,[12]]],SG:[,[,,"[36]\\d{7}|[17-9]\\d{7,10}","\\d{8,11}",,,,,,[8,10,11]],[,,"6[1-9]\\d{6}","\\d{8}",
,,"61234567",,,[8]],[,,"(?:8[1-8]|9[0-8])\\d{6}","\\d{8}",,,"81234567",,,[8]],[,,"1?800\\d{7}","\\d{10,11}",,,"18001234567",,,[10,11]],[,,"1900\\d{7}","\\d{11}",,,"19001234567",,,[11]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"3[12]\\d{6}","\\d{8}",,,"31234567",,,[8]],"SG",65,"0[0-3]\\d",,,,,,,,[[,"([3689]\\d{3})(\\d{4})","$1 $2",["[369]|8[1-9]"]],[,"(1[89]00)(\\d{3})(\\d{4})","$1 $2 $3",["1[89]"]],[,"(7000)(\\d{4})(\\d{3})","$1 $2 $3",["70"]],[,"(800)(\\d{3})(\\d{4})","$1 $2 $3",["80"]]],
,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"7000\\d{7}","\\d{11}",,,"70001234567",,,[11]],,,[,,"NA","NA",,,,,,[-1]]],SH:[,[,,"[256]\\d{4}","\\d{4,5}",,,,,,[4,5]],[,,"2(?:[0-57-9]\\d|6[4-9])\\d{2}","\\d{5}",,,"22158"],[,,"[56]\\d{4}","\\d{5}",,,,,,[5]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"262\\d{2}","\\d{5}",,,,,,[5]],"SH",290,"00",,,,,,,,,,[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA",
"NA",,,,,,[-1]]],SI:[,[,,"[1-7]\\d{6,7}|[89]\\d{4,7}","\\d{5,8}",,,,,,[5,6,7,8]],[,,"(?:1\\d|[25][2-8]|3[24-8]|4[24-8]|7[3-8])\\d{6}","\\d{7,8}",,,"11234567",,,[8]],[,,"(?:[37][01]|4[0139]|51|6[48])\\d{6}","\\d{8}",,,"31234567",,,[8]],[,,"80\\d{4,6}","\\d{6,8}",,,"80123456",,,[6,7,8]],[,,"90\\d{4,6}|89[1-3]\\d{2,5}","\\d{5,8}",,,"90123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"(?:59|8[1-3])\\d{6}","\\d{8}",,,"59012345",,,[8]],"SI",386,"00","0",,,"0",,,,[[,"(\\d)(\\d{3})(\\d{2})(\\d{2})",
"$1 $2 $3 $4",["[12]|3[24-8]|4[24-8]|5[2-8]|7[3-8]"],"(0$1)"],[,"([3-7]\\d)(\\d{3})(\\d{3})","$1 $2 $3",["[37][01]|4[0139]|51|6"],"0$1"],[,"([89][09])(\\d{3,6})","$1 $2",["[89][09]"],"0$1"],[,"([58]\\d{2})(\\d{5})","$1 $2",["59|8[1-3]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],SJ:[,[,,"0\\d{4}|[4789]\\d{7}","\\d{5}(?:\\d{3})?",,,,,,[5,8]],[,,"79\\d{6}","\\d{8}",,,"79123456",,,[8]],[,,"(?:4[015-8]|5[89]|9\\d)\\d{6}","\\d{8}",,,"41234567",
,,[8]],[,,"80[01]\\d{5}","\\d{8}",,,"80012345",,,[8]],[,,"82[09]\\d{5}","\\d{8}",,,"82012345",,,[8]],[,,"810(?:0[0-6]|[2-8]\\d)\\d{3}","\\d{8}",,,"81021234",,,[8]],[,,"880\\d{5}","\\d{8}",,,"88012345",,,[8]],[,,"85[0-5]\\d{5}","\\d{8}",,,"85012345",,,[8]],"SJ",47,"00",,,,,,,,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"0\\d{4}|81(?:0(?:0[7-9]|1\\d)|5\\d{2})\\d{3}","\\d{5}(?:\\d{3})?",,,"01234"],1,,[,,"81[23]\\d{5}","\\d{8}",,,"81212345",,,[8]]],SK:[,[,,"(?:[2-68]\\d{5,8}|9\\d{6,8})","\\d{6,9}",
,,,,,[6,7,9]],[,,"2(?:16\\d{3,4}|\\d{8})|[3-5](?:[1-8]16\\d{2,3}|\\d{8})","\\d{6,9}",,,"212345678"],[,,"9(?:0(?:[1-8]\\d|9[1-9])|(?:1[0-24-9]|4[04589]|50)\\d)\\d{5}","\\d{9}",,,"912123456",,,[9]],[,,"800\\d{6}","\\d{9}",,,"800123456",,,[9]],[,,"9(?:[78]\\d{7}|00\\d{6})","\\d{9}",,,"900123456",,,[9]],[,,"8[5-9]\\d{7}","\\d{9}",,,"850123456",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"6(?:02|5[0-4]|9[0-6])\\d{6}","\\d{9}",,,"690123456",,,[9]],"SK",421,"00","0",,,"0",,,,[[,"(2)(16)(\\d{3,4})","$1 $2 $3",["216"],
"0$1"],[,"([3-5]\\d)(16)(\\d{2,3})","$1 $2 $3",["[3-5]"],"0$1"],[,"(2)(\\d{3})(\\d{3})(\\d{2})","$1/$2 $3 $4",["2"],"0$1"],[,"([3-5]\\d)(\\d{3})(\\d{2})(\\d{2})","$1/$2 $3 $4",["[3-5]"],"0$1"],[,"([689]\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["[689]"],"0$1"],[,"(9090)(\\d{3})","$1 $2",["9090"],"0$1"]],,[,,"9090\\d{3}","\\d{7}",,,"9090123",,,[7]],,,[,,"(?:602|8(?:00|[5-9]\\d)|9(?:00|[78]\\d))\\d{6}|9090\\d{3}","\\d{7,9}",,,"800123456",,,[7,9]],[,,"96\\d{7}","\\d{9}",,,"961234567",,,[9]],,,[,,"NA","NA",
,,,,,[-1]]],SL:[,[,,"[2-9]\\d{7}","\\d{6,8}",,,,,,[8],[6]],[,,"[235]2[2-4][2-9]\\d{4}","\\d{6,8}",,,"22221234"],[,,"(?:2[15]|3[03-5]|4[04]|5[05]|66|7[6-9]|88|99)\\d{6}","\\d{6,8}",,,"25123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"SL",232,"00","0",,,"0",,,,[[,"(\\d{2})(\\d{6})","$1 $2",,"(0$1)"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],SM:[,[,,"[05-7]\\d{7,9}",
"\\d{6,10}",,,,,,[8,10],[6]],[,,"0549(?:8[0157-9]|9\\d)\\d{4}","\\d{6,10}",,,"0549886377",,,[10]],[,,"6[16]\\d{6}","\\d{8}",,,"66661212",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"7[178]\\d{6}","\\d{8}",,,"71123456",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"5[158]\\d{6}","\\d{8}",,,"58001110",,,[8]],"SM",378,"00",,,,"(?:0549)?([89]\\d{5})","0549$1",,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[5-7]"]],[,"(0549)(\\d{6})","$1 $2",["0"]],[,"(\\d{6})","0549 $1",["[89]"]]],[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})",
"$1 $2 $3 $4",["[5-7]"]],[,"(0549)(\\d{6})","($1) $2",["0"]],[,"(\\d{6})","(0549) $1",["[89]"]]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],SN:[,[,,"[3789]\\d{8}","\\d{9}",,,,,,[9]],[,,"3(?:0(?:1[0-2]|80)|282|3(?:8[1-9]|9[3-9])|611)\\d{5}","\\d{9}",,,"301012345"],[,,"7(?:[06-8]\\d|21|90)\\d{6}","\\d{9}",,,"701234567"],[,,"800\\d{6}","\\d{9}",,,"800123456"],[,,"88[4689]\\d{6}","\\d{9}",,,"884123456"],[,,"81[02468]\\d{6}","\\d{9}",,,"810123456"],
[,,"NA","NA",,,,,,[-1]],[,,"39[01]\\d{6}|3392\\d{5}|93330\\d{4}","\\d{9}",,,"933301234"],"SN",221,"00",,,,,,,,[[,"(\\d{2})(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[379]"]],[,"(\\d{3})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4",["8"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],SO:[,[,,"[1-79]\\d{6,8}","\\d{7,9}",,,,,,[7,8,9]],[,,"(?:1\\d|2[0-79]|3[0-46-8]|4[0-7]|59)\\d{5}","\\d{7}",,,"4012345",,,[7]],[,,"(?:15\\d|2(?:4\\d|8)|6[1-35-9]?\\d{2}|7(?:[1-8]\\d|99?\\d)|9(?:0[67]|[2-9])\\d)\\d{5}",
"\\d{7,9}",,,"71123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"SO",252,"00","0",,,"0",,,,[[,"(\\d)(\\d{6})","$1 $2",["2[0-79]|[13-5]"]],[,"(\\d)(\\d{7})","$1 $2",["24|[67]"]],[,"(\\d{2})(\\d{5,7})","$1 $2",["15|28|6[1-35-9]|799|9[2-9]"]],[,"(90\\d)(\\d{3})(\\d{3})","$1 $2 $3",["90"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],SR:[,[,,"[2-8]\\d{5,6}","\\d{6,7}",
,,,,,[6,7]],[,,"(?:2[1-3]|3[0-7]|4\\d|5[2-58]|68\\d)\\d{4}","\\d{6,7}",,,"211234"],[,,"(?:7[124-7]|8[1-9])\\d{5}","\\d{7}",,,"7412345",,,[7]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"5(?:6\\d{4}|90[0-4]\\d{3})","\\d{6,7}",,,"561234"],"SR",597,"00",,,,,,,,[[,"(\\d{3})(\\d{3})","$1-$2",["[2-4]|5[2-58]"]],[,"(\\d{2})(\\d{2})(\\d{2})","$1-$2-$3",["56"]],[,"(\\d{3})(\\d{4})","$1-$2",["59|[6-8]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],
[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],SS:[,[,,"[19]\\d{8}","\\d{9}",,,,,,[9]],[,,"18\\d{7}","\\d{9}",,,"181234567"],[,,"(?:12|9[1257])\\d{7}","\\d{9}",,,"977123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"SS",211,"00","0",,,"0",,,,[[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3",,"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],ST:[,[,,"[29]\\d{6}","\\d{7}",
,,,,,[7]],[,,"22\\d{5}","\\d{7}",,,"2221234"],[,,"9(?:0(?:0[5-9]|[1-9]\\d)|[89]\\d{2})\\d{3}","\\d{7}",,,"9812345"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"ST",239,"00",,,,,,,,[[,"(\\d{3})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],SV:[,[,,"[267]\\d{7}|[89]\\d{6}(?:\\d{4})?","\\d{7,8}|\\d{11}",,,,,,[7,8,11]],[,,"2[1-6]\\d{6}","\\d{8}",,,"21234567",
,,[8]],[,,"[67]\\d{7}","\\d{8}",,,"70123456",,,[8]],[,,"800\\d{4}(?:\\d{4})?","\\d{7}(?:\\d{4})?",,,"8001234",,,[7,11]],[,,"900\\d{4}(?:\\d{4})?","\\d{7}(?:\\d{4})?",,,"9001234",,,[7,11]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"SV",503,"00",,,,,,,,[[,"(\\d{4})(\\d{4})","$1 $2",["[267]"]],[,"(\\d{3})(\\d{4})","$1 $2",["[89]"]],[,"(\\d{3})(\\d{4})(\\d{4})","$1 $2 $3",["[89]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",
,,,,,[-1]]],SX:[,[,,"[5789]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"7215(?:4[2-8]|8[239]|9[056])\\d{4}","\\d{7}(?:\\d{3})?",,,"7215425678"],[,,"7215(?:1[02]|2\\d|5[034679]|8[014-8])\\d{4}","\\d{7}(?:\\d{3})?",,,"7215205678"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002123456"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002123456"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"SX",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,
,,,[-1]],,"721",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],SY:[,[,,"[1-59]\\d{7,8}","\\d{6,9}",,,,,,[8,9],[6,7]],[,,"(?:1(?:1\\d?|4\\d|[2356])|2(?:1\\d?|[235])|3(?:[13]\\d|4)|4[13]|5[1-3])\\d{6}","\\d{6,9}",,,"112345678"],[,,"9(?:22|[3-589]\\d|6[024-9])\\d{6}","\\d{9}",,,"944567890",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"SY",963,"00","0",,,"0",,,,[[,"(\\d{2})(\\d{3})(\\d{3,4})",
"$1 $2 $3",["[1-5]"],"0$1",,1],[,"(9\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["9"],"0$1",,1]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],SZ:[,[,,"[027]\\d{7}","\\d{8}",,,,,,[8]],[,,"2[2-5]\\d{6}","\\d{8}",,,"22171234"],[,,"7[6-8]\\d{6}","\\d{8}",,,"76123456"],[,,"0800\\d{4}","\\d{8}",,,"08001234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"SZ",268,"00",,,,,,,,[[,"(\\d{4})(\\d{4})","$1 $2",["[027]"]]],
,[,,"NA","NA",,,,,,[-1]],,,[,,"0800\\d{4}","\\d{8}",,,"08001234"],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],TA:[,[,,"8\\d{3}","\\d{4}",,,,,,[-1,4]],[,,"8\\d{3}","\\d{4}",,,"8999",,,[4]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"TA",290,"00",,,,,,,,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],TC:[,[,,"[5689]\\d{9}","\\d{7}(?:\\d{3})?",
,,,,,[10],[7]],[,,"649(?:712|9(?:4\\d|50))\\d{4}","\\d{7}(?:\\d{3})?",,,"6497121234"],[,,"649(?:2(?:3[129]|4[1-7])|3(?:3[1-389]|4[1-8])|4[34][1-3])\\d{4}","\\d{7}(?:\\d{3})?",,,"6492311234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002345678"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002345678"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"64971[01]\\d{4}","\\d{10}",,,"6497101234"],"TC",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"649",[,
,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],TD:[,[,,"[2679]\\d{7}","\\d{8}",,,,,,[8]],[,,"22(?:[3789]0|5[0-5]|6[89])\\d{4}","\\d{8}",,,"22501234"],[,,"(?:6[023568]\\d|77\\d|9\\d{2})\\d{5}","\\d{8}",,,"63012345"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"TD",235,"00|16",,,,,,"00",,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA",
"NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],TG:[,[,,"[29]\\d{7}","\\d{8}",,,,,,[8]],[,,"2(?:2[2-7]|3[23]|44|55|66|77)\\d{5}","\\d{8}",,,"22212345"],[,,"9[0-389]\\d{6}","\\d{8}",,,"90112345"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"TG",228,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3 $4"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],TH:[,[,,"[2-9]\\d{7,8}|1\\d{3}(?:\\d{5,6})?",
"\\d{4}|\\d{8,10}",,,,,,[4,8,9,10]],[,,"(?:2\\d|3[2-9]|4[2-5]|5[2-6]|7[3-7])\\d{6}","\\d{8}",,,"21234567",,,[8]],[,,"(?:14|6[1-5]|[89]\\d)\\d{7}","\\d{9}",,,"812345678",,,[9]],[,,"1800\\d{6}","\\d{10}",,,"1800123456",,,[10]],[,,"1900\\d{6}","\\d{10}",,,"1900123456",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"6[08]\\d{7}","\\d{9}",,,"601234567",,,[9]],"TH",66,"00","0",,,"0",,,,[[,"(2)(\\d{3})(\\d{4})","$1 $2 $3",["2"],"0$1"],[,"([13-9]\\d)(\\d{3})(\\d{3,4})","$1 $2 $3",["14|[3-9]"],
"0$1"],[,"(1[89]00)(\\d{3})(\\d{3})","$1 $2 $3",["1"],"$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"1\\d{3}","\\d{4}",,,"1100",,,[4]],[,,"1\\d{3}","\\d{4}",,,"1100",,,[4]],,,[,,"NA","NA",,,,,,[-1]]],TJ:[,[,,"[3-589]\\d{8}","\\d{3,9}",,,,,,[9],[3,5,7]],[,,"(?:3(?:1[3-5]|2[245]|3[12]|4[24-7]|5[25]|72)|4(?:46|74|87))\\d{6}","\\d{3,9}",,,"372123456"],[,,"(?:41[18]|5(?:0[125]|5\\d)|88\\d|9[0-35-9]\\d)\\d{6}","\\d{9}",,,"917123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA",
"NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"TJ",992,"810","8",,,"8",,"8~10",,[[,"([349]\\d{2})(\\d{2})(\\d{4})","$1 $2 $3",["[34]7|91[78]"],"(8) $1",,1],[,"([4589]\\d)(\\d{3})(\\d{4})","$1 $2 $3",["4[148]|[58]|9(?:1[59]|[0235-9])"],"(8) $1",,1],[,"(331700)(\\d)(\\d{2})","$1 $2 $3",["331","3317","33170","331700"],"(8) $1",,1],[,"(\\d{4})(\\d)(\\d{4})","$1 $2 $3",["3[1-5]","3(?:[1245]|3(?:[02-9]|1[0-589]))"],"(8) $1",,1]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,
"NA","NA",,,,,,[-1]]],TK:[,[,,"[2-47]\\d{3,6}","\\d{4,7}",,,,,,[4,5,6,7]],[,,"(?:2[2-4]|[34]\\d)\\d{2,5}","\\d{4,7}",,,"3101"],[,,"7[2-4]\\d{2,5}","\\d{4,7}",,,"7290"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"TK",690,"00",,,,,,,,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],TL:[,[,,"[2-489]\\d{6}|7\\d{6,7}","\\d{7,8}",,,,,,[7,8]],[,,"(?:2[1-5]|3[1-9]|4[1-4])\\d{5}",
"\\d{7}",,,"2112345",,,[7]],[,,"7[3-8]\\d{6}","\\d{8}",,,"77212345",,,[8]],[,,"80\\d{5}","\\d{7}",,,"8012345",,,[7]],[,,"90\\d{5}","\\d{7}",,,"9012345",,,[7]],[,,"NA","NA",,,,,,[-1]],[,,"70\\d{5}","\\d{7}",,,"7012345",,,[7]],[,,"NA","NA",,,,,,[-1]],"TL",670,"00",,,,,,,,[[,"(\\d{3})(\\d{4})","$1 $2",["[2-489]"]],[,"(\\d{4})(\\d{4})","$1 $2",["7"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],TM:[,[,,"[1-6]\\d{7}","\\d{8}",,,,,,[8]],[,,"(?:1(?:2\\d|3[1-9])|2(?:22|4[0-35-8])|3(?:22|4[03-9])|4(?:22|3[128]|4\\d|6[15])|5(?:22|5[7-9]|6[014-689]))\\d{5}",
"\\d{8}",,,"12345678"],[,,"6[1-9]\\d{6}","\\d{8}",,,"66123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"TM",993,"810","8",,,"8",,"8~10",,[[,"(\\d{2})(\\d{2})(\\d{2})(\\d{2})","$1 $2-$3-$4",["12"],"(8 $1)"],[,"(\\d{2})(\\d{6})","$1 $2",["6"],"8 $1"],[,"(\\d{3})(\\d)(\\d{2})(\\d{2})","$1 $2-$3-$4",["13|[2-5]"],"(8 $1)"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],
TN:[,[,,"[2-57-9]\\d{7}","\\d{8}",,,,,,[8]],[,,"3(?:[012]\\d|6[0-4]|91)\\d{5}|7\\d{7}|81200\\d{3}","\\d{8}",,,"71234567"],[,,"(?:[259]\\d|4[0-6])\\d{6}","\\d{8}",,,"20123456"],[,,"8010\\d{4}","\\d{8}",,,"80101234"],[,,"88\\d{6}","\\d{8}",,,"88123456"],[,,"8[12]10\\d{4}","\\d{8}",,,"81101234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"TN",216,"00",,,,,,,,[[,"(\\d{2})(\\d{3})(\\d{3})","$1 $2 $3"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,
[-1]]],TO:[,[,,"[02-8]\\d{4,6}","\\d{5,7}",,,,,,[5,7]],[,,"(?:2\\d|3[1-8]|4[1-4]|[56]0|7[0149]|8[05])\\d{3}","\\d{5}",,,"20123",,,[5]],[,,"(?:7[578]|8[47-9])\\d{5}","\\d{7}",,,"7715123",,,[7]],[,,"0800\\d{3}","\\d{7}",,,"0800222",,,[7]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"TO",676,"00",,,,,,,,[[,"(\\d{2})(\\d{3})","$1-$2",["[1-6]|7[0-4]|8[05]"]],[,"(\\d{3})(\\d{4})","$1 $2",["7[5-9]|8[47-9]"]],[,"(\\d{4})(\\d{3})","$1 $2",["0"]]],,[,,"NA",
"NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],TR:[,[,,"[2-589]\\d{9}|444\\d{4}","\\d{7,10}",,,,,,[7,10]],[,,"(?:2(?:[13][26]|[28][2468]|[45][268]|[67][246])|3(?:[13][28]|[24-6][2468]|[78][02468]|92)|4(?:[16][246]|[23578][2468]|4[26]))\\d{7}","\\d{10}",,,"2123456789",,,[10]],[,,"5(?:(?:0[1-7]|22|[34]\\d|5[1-59]|9[246])\\d{2}|6161)\\d{5}","\\d{10}",,,"5012345678",,,[10]],[,,"800\\d{7}","\\d{10}",,,"8001234567",,,[10]],[,,"900\\d{7}","\\d{10}",,,"9001234567",
,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"TR",90,"00","0",,,"0",,,,[[,"(\\d{3})(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3 $4",["[23]|4(?:[0-35-9]|4[0-35-9])"],"(0$1)",,1],[,"(\\d{3})(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3 $4",["5[02-69]"],"0$1",,1],[,"(\\d{3})(\\d{3})(\\d{4})","$1 $2 $3",["51|[89]"],"0$1",,1],[,"(444)(\\d{1})(\\d{3})","$1 $2 $3",["444"]]],,[,,"512\\d{7}","\\d{10}",,,"5123456789",,,[10]],,,[,,"444\\d{4}","\\d{7}",,,"4441444",,,[7]],[,,"444\\d{4}|850\\d{7}",
"\\d{7,10}",,,"4441444"],,,[,,"NA","NA",,,,,,[-1]]],TT:[,[,,"[589]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"868(?:2(?:01|2[1-6]|3[1-5])|6(?:0[79]|1[02-8]|2[1-9]|[3-69]\\d|7[0-79])|82[124])\\d{4}","\\d{7}(?:\\d{3})?",,,"8682211234"],[,,"868(?:2(?:[789]\\d)|3(?:0[1-9]|1[02-9]|[2-9]\\d)|4[6-9]\\d|6(?:20|78|8\\d)|7(?:0[1-9]|1[02-9]|[2-9]\\d))\\d{4}","\\d{7}(?:\\d{3})?",,,"8682911234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002345678"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002345678"],[,
,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"TT",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"868",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"868619\\d{4}","\\d{10}",,,"8686191234"]],TV:[,[,,"[279]\\d{4,6}","\\d{5,7}",,,,,,[5,6,7]],[,,"2[02-9]\\d{3}","\\d{5}",,,"20123",,,[5]],[,,"(?:70\\d|90)\\d{4}","\\d{6,7}",,,"901234",,,[6,7]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",
,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"TV",688,"00",,,,,,,,,,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],TW:[,[,,"[2-689]\\d{7,8}|7\\d{7,9}","\\d{8,10}",,,,,,[8,9,10]],[,,"[2-8]\\d{7,8}","\\d{8,9}",,,"21234567",,,[8,9]],[,,"9\\d{8}","\\d{9}",,,"912345678",,,[9]],[,,"800\\d{6}","\\d{9}",,,"800123456",,,[9]],[,,"900\\d{6}","\\d{9}",,,"900123456",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"70\\d{8}","\\d{10}",,,"7012345678",,,[10]],
"TW",886,"0(?:0[25679]|19)","0","#",,"0",,,,[[,"([2-8])(\\d{3,4})(\\d{4})","$1 $2 $3",["[2-6]|[78][1-9]"],"0$1"],[,"([89]\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["80|9"],"0$1"],[,"(70)(\\d{4})(\\d{4})","$1 $2 $3",["70"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],TZ:[,[,,"\\d{9}","\\d{7,9}",,,,,,[7,9]],[,,"2[2-8]\\d{7}","\\d{7,9}",,,"222345678"],[,,"(?:6[125-9]|7[1-9])\\d{7}","\\d{9}",,,"621234567",,,[9]],[,,"80[08]\\d{6}","\\d{9}",,,"800123456",
,,[9]],[,,"90\\d{7}","\\d{9}",,,"900123456",,,[9]],[,,"8(?:40|6[01])\\d{6}","\\d{9}",,,"840123456",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"41\\d{7}","\\d{9}",,,"412345678",,,[9]],"TZ",255,"00[056]","0",,,"0",,,,[[,"([24]\\d)(\\d{3})(\\d{4})","$1 $2 $3",["[24]"],"0$1"],[,"([67]\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["[67]"],"0$1"],[,"([89]\\d{2})(\\d{2})(\\d{4})","$1 $2 $3",["[89]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],UA:[,[,,"[3-9]\\d{8}",
"\\d{5,9}",,,,,,[9],[5,6,7]],[,,"(?:3[1-8]|4[13-8]|5[1-7]|6[12459])\\d{7}","\\d{5,9}",,,"311234567"],[,,"(?:39|50|6[36-8]|73|9[1-9])\\d{7}","\\d{9}",,,"391234567"],[,,"800\\d{6}","\\d{9}",,,"800123456"],[,,"900\\d{6}","\\d{9}",,,"900123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"89\\d{7}","\\d{9}",,,"891234567"],"UA",380,"00","0",,,"0",,"0~0",,[[,"([3-9]\\d)(\\d{3})(\\d{4})","$1 $2 $3",["[38]9|4(?:[45][0-5]|87)|5(?:0|6[37]|7[37])|6[36-8]|73|9[1-9]","[38]9|4(?:[45][0-5]|87)|5(?:0|6(?:3[14-7]|7)|7[37])|6[36-8]|73|9[1-9]"],
"0$1"],[,"([3-689]\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["3[1-8]2|4[13678]2|5(?:[12457]2|6[24])|6(?:[49]2|[12][29]|5[24])|8[0-8]|90","3(?:[1-46-8]2[013-9]|52)|4(?:[1378]2|62[013-9])|5(?:[12457]2|6[24])|6(?:[49]2|[12][29]|5[24])|8[0-8]|90"],"0$1"],[,"([3-6]\\d{3})(\\d{5})","$1 $2",["3(?:5[013-9]|[1-46-8])|4(?:[137][013-9]|6|[45][6-9]|8[4-6])|5(?:[1245][013-9]|6[0135-9]|3|7[4-6])|6(?:[49][013-9]|5[0135-9]|[12][13-8])","3(?:5[013-9]|[1-46-8](?:22|[013-9]))|4(?:[137][013-9]|6(?:[013-9]|22)|[45][6-9]|8[4-6])|5(?:[1245][013-9]|6(?:3[02389]|[015689])|3|7[4-6])|6(?:[49][013-9]|5[0135-9]|[12][13-8])"],
"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],UG:[,[,,"\\d{9}","\\d{5,9}",,,,,,[9],[5,6,7]],[,,"20(?:[0147]\\d{2}|2(?:40|[5-9]\\d)|3(?:0[0-4]|[23]\\d)|5[0-4]\\d|6[035-9]\\d|8[0-2]\\d)\\d{4}|[34]\\d{8}","\\d{5,9}",,,"312345678"],[,,"7(?:(?:0[0-7]|[15789]\\d|30|4[0-4])\\d|2(?:[03]\\d|60))\\d{5}","\\d{9}",,,"712345678"],[,,"800[123]\\d{5}","\\d{9}",,,"800123456"],[,,"90[123]\\d{6}","\\d{9}",,,"901123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA",
"NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"UG",256,"00[057]","0",,,"0",,,,[[,"(\\d{3})(\\d{6})","$1 $2",["[7-9]|20(?:[013-8]|2[5-9])|4(?:6[45]|[7-9])"],"0$1"],[,"(\\d{2})(\\d{7})","$1 $2",["3|4(?:[1-5]|6[0-36-9])"],"0$1"],[,"(2024)(\\d{5})","$1 $2",["2024"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],US:[,[,,"[2-9]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"(?:2(?:0[1-35-9]|1[02-9]|2[04589]|3[149]|4[08]|5[1-46]|6[0279]|7[026]|8[13])|3(?:0[1-57-9]|1[02-9]|2[0135]|3[014679]|4[67]|5[12]|6[014]|8[056])|4(?:0[124-9]|1[02-579]|2[3-5]|3[0245]|4[0235]|58|6[39]|7[0589]|8[04])|5(?:0[1-57-9]|1[0235-8]|20|3[0149]|4[01]|5[19]|6[1-37]|7[013-5]|8[056])|6(?:0[1-35-9]|1[024-9]|2[03689]|3[016]|4[16]|5[017]|6[0-279]|78|8[12])|7(?:0[1-46-8]|1[02-9]|2[0457]|3[1247]|4[037]|5[47]|6[02359]|7[02-59]|8[156])|8(?:0[1-68]|1[02-8]|28|3[0-25]|4[3578]|5[046-9]|6[02-5]|7[028])|9(?:0[1346-9]|1[02-9]|2[0589]|3[014678]|4[0179]|5[12469]|7[0-3589]|8[0459]))[2-9]\\d{6}",
"\\d{7}(?:\\d{3})?",,,"2015550123"],[,,"(?:2(?:0[1-35-9]|1[02-9]|2[04589]|3[149]|4[08]|5[1-46]|6[0279]|7[026]|8[13])|3(?:0[1-57-9]|1[02-9]|2[0135]|3[014679]|4[67]|5[12]|6[014]|8[056])|4(?:0[124-9]|1[02-579]|2[3-5]|3[0245]|4[0235]|58|6[39]|7[0589]|8[04])|5(?:0[1-57-9]|1[0235-8]|20|3[0149]|4[01]|5[19]|6[1-37]|7[013-5]|8[056])|6(?:0[1-35-9]|1[024-9]|2[03689]|3[016]|4[16]|5[017]|6[0-279]|78|8[12])|7(?:0[1-46-8]|1[02-9]|2[0457]|3[1247]|4[037]|5[47]|6[02359]|7[02-59]|8[156])|8(?:0[1-68]|1[02-8]|28|3[0-25]|4[3578]|5[046-9]|6[02-5]|7[028])|9(?:0[1346-9]|1[02-9]|2[0589]|3[014678]|4[0179]|5[12469]|7[0-3589]|8[0459]))[2-9]\\d{6}",
"\\d{7}(?:\\d{3})?",,,"2015550123"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002345678"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002345678"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"US",1,"011","1",,,"1",,,1,[[,"(\\d{3})(\\d{4})","$1-$2",,,,1],[,"(\\d{3})(\\d{3})(\\d{4})","($1) $2-$3",,,,1]],[[,"(\\d{3})(\\d{3})(\\d{4})","$1-$2-$3"]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA",
"NA",,,,,,[-1]]],UY:[,[,,"[2489]\\d{6,7}","\\d{7,8}",,,,,,[7,8]],[,,"2\\d{7}|4[2-7]\\d{6}","\\d{7,8}",,,"21231234",,,[8]],[,,"9[1-9]\\d{6}","\\d{8}",,,"94231234",,,[8]],[,,"80[05]\\d{4}","\\d{7}",,,"8001234",,,[7]],[,,"90[0-8]\\d{4}","\\d{7}",,,"9001234",,,[7]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"UY",598,"0(?:1[3-9]\\d|0)","0"," int. ",,"0",,"00",,[[,"(\\d{4})(\\d{4})","$1 $2",["[24]"]],[,"(\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["9[1-9]"],"0$1"],[,"(\\d{3})(\\d{4})",
"$1 $2",["[89]0"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],UZ:[,[,,"[679]\\d{8}","\\d{7,9}",,,,,,[9],[7]],[,,"(?:6(?:1(?:22|3[124]|4[1-4]|5[123578]|64)|2(?:22|3[0-57-9]|41)|5(?:22|3[3-7]|5[024-8])|6\\d{2}|7(?:[23]\\d|7[69])|9(?:22|4[1-8]|6[135]))|7(?:0(?:5[4-9]|6[0146]|7[12456]|9[135-8])|1[12]\\d|2(?:22|3[1345789]|4[123579]|5[14])|3(?:2\\d|3[1578]|4[1-35-7]|5[1-57]|61)|4(?:2\\d|3[1-579]|7[1-79])|5(?:22|5[1-9]|6[1457])|6(?:22|3[12457]|4[13-8])|9(?:22|5[1-9])))\\d{5}",
"\\d{7,9}",,,"662345678"],[,,"6(?:1(?:2(?:98|2[01])|35[0-4]|50\\d|61[23]|7(?:[01][017]|4\\d|55|9[5-9]))|2(?:11\\d|2(?:[12]1|9[01379])|5(?:[126]\\d|3[0-4])|7\\d{2})|5(?:19[01]|2(?:27|9[26])|30\\d|59\\d|7\\d{2})|6(?:2(?:1[5-9]|2[0367]|38|41|52|60)|3[79]\\d|4(?:56|83)|7(?:[07]\\d|1[017]|3[07]|4[047]|5[057]|67|8[0178]|9[79])|9[0-3]\\d)|7(?:2(?:24|3[237]|4[5-9]|7[15-8])|5(?:7[12]|8[0589])|7(?:0\\d|[39][07])|9(?:0\\d|7[079]))|9(?:2(?:1[1267]|5\\d|3[01]|7[0-4])|5[67]\\d|6(?:2[0-26]|8\\d)|7\\d{2}))\\d{4}|7(?:0\\d{3}|1(?:13[01]|6(?:0[47]|1[67]|66)|71[3-69]|98\\d)|2(?:2(?:2[79]|95)|3(?:2[5-9]|6[0-6])|57\\d|7(?:0\\d|1[17]|2[27]|3[37]|44|5[057]|66|88))|3(?:2(?:1[0-6]|21|3[469]|7[159])|33\\d|5(?:0[0-4]|5[579]|9\\d)|7(?:[0-3579]\\d|4[0467]|6[67]|8[078])|9[4-6]\\d)|4(?:2(?:29|5[0257]|6[0-7]|7[1-57])|5(?:1[0-4]|8\\d|9[5-9])|7(?:0\\d|1[024589]|2[0127]|3[0137]|[46][07]|5[01]|7[5-9]|9[079])|9(?:7[015-9]|[89]\\d))|5(?:112|2(?:0\\d|2[29]|[49]4)|3[1568]\\d|52[6-9]|7(?:0[01578]|1[017]|[23]7|4[047]|[5-7]\\d|8[78]|9[079]))|6(?:2(?:2[1245]|4[2-4])|39\\d|41[179]|5(?:[349]\\d|5[0-2])|7(?:0[017]|[13]\\d|22|44|55|67|88))|9(?:22[128]|3(?:2[0-4]|7\\d)|57[05629]|7(?:2[05-9]|3[37]|4\\d|60|7[2579]|87|9[07])))\\d{4}|9[0-57-9]\\d{7}",
"\\d{9}",,,"912345678"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"UZ",998,"810","8",,,"8",,"8~10",,[[,"([679]\\d)(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3 $4",,"8 $1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],VA:[,[,,"(?:0(?:878\\d{5}|6698\\d{5})|[1589]\\d{5,10}|3(?:[12457-9]\\d{8}|[36]\\d{7,9}))","\\d{6,11}",,,,,,[6,8,9,10,11]],[,,"06698\\d{5}","\\d{10}",,,"0669812345",
,,[10]],[,,"3(?:[12457-9]\\d{8}|6\\d{7,8}|3\\d{7,9})","\\d{9,11}",,,"3123456789",,,[9,10,11]],[,,"80(?:0\\d{6}|3\\d{3})","\\d{6,9}",,,"800123456",,,[6,9]],[,,"0878\\d{5}|1(?:44|6[346])\\d{6}|89(?:2\\d{3}|4(?:[0-4]\\d{2}|[5-9]\\d{4})|5(?:[0-4]\\d{2}|[5-9]\\d{6})|9\\d{6})","\\d{6,10}",,,"899123456",,,[6,8,9,10]],[,,"84(?:[08]\\d{6}|[17]\\d{3})","\\d{6,9}",,,"848123456",,,[6,9]],[,,"1(?:78\\d|99)\\d{6}","\\d{9,10}",,,"1781234567",,,[9,10]],[,,"55\\d{8}","\\d{10}",,,"5512345678",,,[10]],"VA",39,"00",
,,,,,,,,,[,,"NA","NA",,,,,,[-1]],,,[,,"848\\d{6}","\\d{9}",,,"848123456",,,[9]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],VC:[,[,,"[5789]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"784(?:266|3(?:6[6-9]|7\\d|8[0-24-6])|4(?:38|5[0-36-8]|8[0-8])|5(?:55|7[0-2]|93)|638|784)\\d{4}","\\d{7}(?:\\d{3})?",,,"7842661234"],[,,"784(?:4(?:3[0-4]|5[45]|89|9[0-58])|5(?:2[6-9]|3[0-4]))\\d{4}","\\d{7}(?:\\d{3})?",,,"7844301234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002345678"],[,,"900[2-9]\\d{6}",
"\\d{10}",,,"9002345678"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"VC",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"784",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],VE:[,[,,"[24589]\\d{9}","\\d{7,10}",,,,,,[10],[7]],[,,"(?:2(?:12|3[457-9]|[58][1-9]|[467]\\d|9[1-6])|50[01])\\d{7}","\\d{7,10}",,,"2121234567"],[,,"4(?:1[24-8]|2[46])\\d{7}","\\d{10}",,,"4121234567"],[,,"800\\d{7}","\\d{10}",
,,"8001234567"],[,,"900\\d{7}","\\d{10}",,,"9001234567"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"VE",58,"00","0",,,"0",,,,[[,"(\\d{3})(\\d{7})","$1-$2",,"0$1","$CC $1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],VG:[,[,,"[2589]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"284(?:(?:229|4(?:22|9[45])|774|8(?:52|6[459]))\\d{4}|496[0-5]\\d{3})","\\d{7}(?:\\d{3})?",,,"2842291234"],[,,"284(?:(?:3(?:0[0-3]|4[0-367]|94)|4(?:4[0-6]|68|99)|54[0-57])\\d{4}|496[6-9]\\d{3})",
"\\d{7}(?:\\d{3})?",,,"2843001234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002345678"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002345678"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"VG",1,"011","1",,,"1",,,,,,[,,"NA","NA",,,,,,[-1]],,"284",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],VI:[,[,,"[3589]\\d{9}","\\d{7}(?:\\d{3})?",,,,,,[10],[7]],[,,"340(?:2(?:01|2[0678]|44|77)|3(?:32|44)|4(?:22|7[34])|5(?:1[34]|55)|6(?:26|4[23]|77|9[023])|7(?:1[2-589]|27|7\\d)|884|998)\\d{4}",
"\\d{7}(?:\\d{3})?",,,"3406421234"],[,,"340(?:2(?:01|2[0678]|44|77)|3(?:32|44)|4(?:22|7[34])|5(?:1[34]|55)|6(?:26|4[23]|77|9[023])|7(?:1[2-589]|27|7\\d)|884|998)\\d{4}","\\d{7}(?:\\d{3})?",,,"3406421234"],[,,"8(?:00|44|55|66|77|88)[2-9]\\d{6}","\\d{10}",,,"8002345678"],[,,"900[2-9]\\d{6}","\\d{10}",,,"9002345678"],[,,"NA","NA",,,,,,[-1]],[,,"5(?:00|22|33|44|66|77|88)[2-9]\\d{6}","\\d{10}",,,"5002345678"],[,,"NA","NA",,,,,,[-1]],"VI",1,"011","1",,,"1",,,1,,,[,,"NA","NA",,,,,,[-1]],,"340",[,,"NA","NA",
,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],VN:[,[,,"[17]\\d{6,9}|[2-69]\\d{7,9}|8\\d{6,8}","\\d{7,10}",,,,,,[7,8,9,10]],[,,"(?:2(?:[025-79]|1[0189]|[348][01])|3(?:[0136-9]|[25][01])|4\\d|5(?:[01][01]|[2-9])|6(?:[0-46-8]|5[01])|7(?:[02-79]|[18][01]))\\d{7}|8(?:[1-57]\\d|[689][0-79])\\d{6}","\\d{9,10}",,,"2101234567",,,[9,10]],[,,"(?:9\\d|1(?:2\\d|6[2-9]|8[68]|99))\\d{7}|8[689]8\\d{6}","\\d{9,10}",,,"912345678",,,[9,10]],[,,"1800\\d{4,6}","\\d{8,10}",,,"1800123456",,,[8,9,10]],[,
,"1900\\d{4,6}","\\d{8,10}",,,"1900123456",,,[8,9,10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"VN",84,"00","0",,,"0",,,,[[,"([17]99)(\\d{4})","$1 $2",["[17]99"],"0$1",,1],[,"([48])(\\d{4})(\\d{4})","$1 $2 $3",["4|8(?:[1-57]|[689][0-79])"],"0$1",,1],[,"([235-7]\\d)(\\d{4})(\\d{3})","$1 $2 $3",["2[025-79]|3[0136-9]|5[2-9]|6[0-46-8]|7[02-79]"],"0$1",,1],[,"(80)(\\d{5})","$1 $2",["80"],"0$1",,1],[,"(69\\d)(\\d{4,5})","$1 $2",["69"],"0$1",,1],[,"([235-7]\\d{2})(\\d{4})(\\d{3})",
"$1 $2 $3",["2[1348]|3[25]|5[01]|65|7[18]"],"0$1",,1],[,"([89]\\d)(\\d{3})(\\d{2})(\\d{2})","$1 $2 $3 $4",["8[689]8|9"],"0$1",,1],[,"(1[2689]\\d)(\\d{3})(\\d{4})","$1 $2 $3",["1(?:[26]|8[68]|99)"],"0$1",,1],[,"(1[89]00)(\\d{4,6})","$1 $2",["1[89]0"],"$1",,1]],,[,,"NA","NA",,,,,,[-1]],,,[,,"[17]99\\d{4}|69\\d{5,6}","\\d{7,8}",,,"1992000",,,[7,8]],[,,"[17]99\\d{4}|69\\d{5,6}|80\\d{5}","\\d{7,8}",,,"1992000",,,[7,8]],,,[,,"NA","NA",,,,,,[-1]]],VU:[,[,,"[2-57-9]\\d{4,6}","\\d{5,7}",,,,,,[5,7]],[,,"(?:2[02-9]\\d|3(?:[5-7]\\d|8[0-8])|48[4-9]|88\\d)\\d{2}",
"\\d{5}",,,"22123",,,[5]],[,,"(?:5(?:7[2-5]|[0-689]\\d)|7[013-7]\\d)\\d{4}","\\d{7}",,,"5912345",,,[7]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"VU",678,"00",,,,,,,,[[,"(\\d{3})(\\d{4})","$1 $2",["[579]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"3[03]\\d{3}|900\\d{4}","\\d{5,7}",,,"30123"],,,[,,"NA","NA",,,,,,[-1]]],WF:[,[,,"[4-8]\\d{5}","\\d{6}",,,,,,[6]],[,,"(?:50|68|72)\\d{4}","\\d{6}",,,"501234"],[,
,"(?:50|68|72|8[23])\\d{4}","\\d{6}",,,"501234"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"WF",681,"00",,,,,,,,[[,"(\\d{2})(\\d{2})(\\d{2})","$1 $2 $3"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"[48]0\\d{4}","\\d{6}",,,"401234"]],WS:[,[,,"[2-8]\\d{4,6}","\\d{5,7}",,,,,,[5,6,7]],[,,"(?:[2-5]\\d|6[1-9]|84\\d{2})\\d{3}","\\d{5,7}",,,"22123",,,[5,7]],[,,"(?:60|7[25-7]\\d)\\d{4}","\\d{6,7}",
,,"601234",,,[6,7]],[,,"800\\d{3}","\\d{6}",,,"800123",,,[6]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"WS",685,"0",,,,,,,,[[,"(8\\d{2})(\\d{3,4})","$1 $2",["8"]],[,"(7\\d)(\\d{5})","$1 $2",["7"]],[,"(\\d{5})","$1",["[2-6]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],YE:[,[,,"[1-7]\\d{6,8}","\\d{6,9}",,,,,,[7,8,9],[6]],[,,"(?:1(?:7\\d|[2-68])|2[2-68]|3[2358]|4[2-58]|5[2-6]|6[3-58]|7[24-68])\\d{5}",
"\\d{6,8}",,,"1234567",,,[7,8]],[,,"7[0137]\\d{7}","\\d{9}",,,"712345678",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"YE",967,"00","0",,,"0",,,,[[,"([1-7])(\\d{3})(\\d{3,4})","$1 $2 $3",["[1-6]|7[24-68]"],"0$1"],[,"(7\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["7[0137]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],YT:[,[,,"[268]\\d{8}","\\d{9}",,,,,,[9]],[,
,"269(?:6[0-4]|50)\\d{4}","\\d{9}",,,"269601234"],[,,"639\\d{6}","\\d{9}",,,"639123456"],[,,"80\\d{7}","\\d{9}",,,"801234567"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"YT",262,"00","0",,,"0",,,,,,[,,"NA","NA",,,,,,[-1]],,"269|63",[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],ZA:[,[,,"[1-79]\\d{8}|8(?:[067]\\d{7}|[1-4]\\d{3,7})","\\d{5,9}",,,,,,[5,6,7,8,9]],[,,"(?:1[0-8]|2[0-378]|3[1-69]|4\\d|5[1346-8])\\d{7}","\\d{9}",
,,"101234567",,,[9]],[,,"(?:6[0-5]|7[0-46-9])\\d{7}|8[1-4]\\d{3,7}","\\d{5,9}",,,"711234567"],[,,"80\\d{7}","\\d{9}",,,"801234567",,,[9]],[,,"86[2-9]\\d{6}|90\\d{7}","\\d{9}",,,"862345678",,,[9]],[,,"860\\d{6}","\\d{9}",,,"860123456",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"87\\d{7}","\\d{9}",,,"871234567",,,[9]],"ZA",27,"00","0",,,"0",,,,[[,"(860)(\\d{3})(\\d{3})","$1 $2 $3",["860"],"0$1"],[,"(\\d{2})(\\d{3})(\\d{4})","$1 $2 $3",["[1-79]|8(?:[0-47]|6[1-9])"],"0$1"],[,"(\\d{2})(\\d{3,4})","$1 $2",["8[1-4]"],
"0$1"],[,"(\\d{2})(\\d{3})(\\d{2,3})","$1 $2 $3",["8[1-4]"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"861\\d{6}","\\d{9}",,,"861123456",,,[9]],,,[,,"NA","NA",,,,,,[-1]]],ZM:[,[,,"[289]\\d{8}","\\d{9}",,,,,,[9]],[,,"21[1-8]\\d{6}","\\d{9}",,,"211234567"],[,,"9(?:5[034589]|[67]\\d)\\d{6}","\\d{9}",,,"955123456"],[,,"800\\d{6}","\\d{9}",,,"800123456"],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"ZM",260,"00","0",,,"0",,,,[[,"([29]\\d)(\\d{7})",
"$1 $2",["[29]"],"0$1"],[,"(800)(\\d{3})(\\d{3})","$1 $2 $3",["8"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],ZW:[,[,,"2(?:[012457-9]\\d{3,8}|6(?:[14]\\d{7}|\\d{4}))|[13-79]\\d{4,9}|8[06]\\d{8}","\\d{3,10}",,,,,,[5,6,7,8,9,10],[3,4]],[,,"(?:2(?:0(?:4\\d|5\\d{2})|2[278]\\d|48\\d|7(?:[1-7]\\d|[089]\\d{2})|8(?:[2-57-9]|[146]\\d{2})|98)|3(?:08|17|3[78]|7(?:[19]|[56]\\d)|8[37]|98)|5[15][78]|6(?:28\\d{2}|[36]7|75\\d|[69]8|8(?:7\\d|8)))\\d{3}|(?:2(?:1[39]|2[0157]|6[14]|7[35]|84)|329)\\d{7}|(?:1(?:3\\d{2}|9\\d|[4-8])|2(?:0\\d{2}|[569]\\d)|3(?:[26]|[013459]\\d)|5(?:0|5\\d{2}|[689]\\d)|6(?:[39]|[01246]\\d|[78]\\d{2}))\\d{3}|(?:29\\d|39|54)\\d{6}|(?:(?:25|54)83|2582\\d)\\d{3}|(?:4\\d{6,7}|9[2-9]\\d{4,5})",
"\\d{3,10}",,,"1312345"],[,,"7[1378]\\d{7}","\\d{9}",,,"711234567"],[,,"800\\d{7}","\\d{10}",,,"8001234567",,,[10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"86(?:1[12]|30|44|55|77|8[367]|99)\\d{6}","\\d{10}",,,"8686123456",,,[10]],"ZW",263,"00","0",,,"0",,,,[[,"([49])(\\d{3})(\\d{2,4})","$1 $2 $3",["4|9[2-9]"],"0$1"],[,"(7\\d)(\\d{3})(\\d{3,4})","$1 $2 $3",["7"],"0$1"],[,"(86\\d{2})(\\d{3})(\\d{3})","$1 $2 $3",["86[24]"],"0$1"],[,"([2356]\\d{2})(\\d{3,5})","$1 $2",
["2(?:0[45]|2[278]|[49]8|[78])|3(?:08|17|3[78]|7[1569]|8[37]|98)|5[15][78]|6(?:[29]8|[38]7|6[78]|75|[89]8)"],"0$1"],[,"(\\d{3})(\\d{3})(\\d{3,4})","$1 $2 $3",["2(?:1[39]|2[0157]|6[14]|7[35]|84)|329"],"0$1"],[,"([1-356]\\d)(\\d{3,5})","$1 $2",["1[3-9]|2[0569]|3[0-69]|5[05689]|6[0-46-9]"],"0$1"],[,"([235]\\d)(\\d{3})(\\d{3,4})","$1 $2 $3",["[23]9|54"],"0$1"],[,"([25]\\d{3})(\\d{3,5})","$1 $2",["(?:25|54)8","258[23]|5483"],"0$1"],[,"(8\\d{3})(\\d{6})","$1 $2",["86"],"0$1"],[,"(80\\d)(\\d{3})(\\d{4})",
"$1 $2 $3",["80"],"0$1"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],800:[,[,,"\\d{8}","\\d{8}",,,"12345678",,,[-1,8]],[,,"NA","NA",,,"12345678",,,[-1]],[,,"NA","NA",,,"12345678",,,[-1]],[,,"\\d{8}","\\d{8}",,,"12345678",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"001",800,,,,,,,,1,[[,"(\\d{4})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,
,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],808:[,[,,"\\d{8}","\\d{8}",,,"12345678",,,[-1,8]],[,,"NA","NA",,,"12345678",,,[-1]],[,,"NA","NA",,,"12345678",,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"\\d{8}","\\d{8}",,,"12345678",,,[8]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"001",808,,,,,,,,1,[[,"(\\d{4})(\\d{4})","$1 $2"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]],870:[,[,,"[35-7]\\d{8}","\\d{9}",,,"301234567",,,[-1,9]],
[,,"NA","NA",,,"301234567",,,[-1]],[,,"(?:[356]\\d|7[6-8])\\d{7}","\\d{9}",,,"301234567",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"001",870,,,,,,,,,[[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],878:[,[,,"1\\d{11}","\\d{12}",,,"101234567890",,,[-1,12]],[,,"NA","NA",,,"101234567890",,,[-1]],[,,"NA","NA",,,"101234567890",
,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"10\\d{10}","\\d{12}",,,"101234567890",,,[12]],"001",878,,,,,,,,1,[[,"(\\d{2})(\\d{5})(\\d{5})","$1 $2 $3"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],881:[,[,,"[67]\\d{8}","\\d{9}",,,"612345678",,,[-1,9]],[,,"NA","NA",,,"612345678",,,[-1]],[,,"[67]\\d{8}","\\d{9}",,,"612345678",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],
[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"001",881,,,,,,,,,[[,"(\\d)(\\d{3})(\\d{5})","$1 $2 $3",["[67]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],882:[,[,,"[13]\\d{6,11}","\\d{7,12}",,,"3451234567",,,[-1,7,8,9,10,11,12]],[,,"NA","NA",,,"3451234567",,,[-1]],[,,"3(?:2\\d{3}|37\\d{2}|4(?:2|7\\d{3}))\\d{4}","\\d{7,10}",,,"3451234567",,,[7,9,10]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,
,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"1(?:3(?:0[0347]|[13][0139]|2[035]|4[013568]|6[0459]|7[06]|8[15678]|9[0689])\\d{4}|6\\d{5,10})|3(?:45|9\\d{3})\\d{7}","\\d{7,12}",,,"390123456789",,,[7,8,9,10,11,12]],"001",882,,,,,,,,,[[,"(\\d{2})(\\d{4})(\\d{3})","$1 $2 $3",["3[23]"]],[,"(\\d{2})(\\d{5})","$1 $2",["16|342"]],[,"(\\d{2})(\\d{4})(\\d{4})","$1 $2 $3",["34[57]"]],[,"(\\d{3})(\\d{4})(\\d{4})","$1 $2 $3",["348"]],[,"(\\d{2})(\\d{2})(\\d{4})","$1 $2 $3",["1"]],[,"(\\d{2})(\\d{3,4})(\\d{4})","$1 $2 $3",
["16"]],[,"(\\d{2})(\\d{4,5})(\\d{5})","$1 $2 $3",["16|39"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"348[57]\\d{7}","\\d{11}",,,"3451234567",,,[11]]],883:[,[,,"51\\d{7}(?:\\d{3})?","\\d{9}(?:\\d{3})?",,,"510012345",,,[-1,9,12]],[,,"NA","NA",,,"510012345",,,[-1]],[,,"NA","NA",,,"510012345",,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"51(?:00\\d{5}(?:\\d{3})?|[13]0\\d{8})","\\d{9}(?:\\d{3})?",,,
"510012345",,,[9,12]],"001",883,,,,,,,,1,[[,"(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3",["510"]],[,"(\\d{3})(\\d{3})(\\d{3})(\\d{3})","$1 $2 $3 $4",["510"]],[,"(\\d{4})(\\d{4})(\\d{4})","$1 $2 $3",["51[13]"]]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]]],888:[,[,,"\\d{11}","\\d{11}",,,"12345678901",,,[-1,11]],[,,"NA","NA",,,"12345678901",,,[-1]],[,,"NA","NA",,,"12345678901",,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,
,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],"001",888,,,,,,,,1,[[,"(\\d{3})(\\d{3})(\\d{5})","$1 $2 $3"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"\\d{11}","\\d{11}",,,"12345678901",,,[11]],1,,[,,"NA","NA",,,,,,[-1]]],979:[,[,,"\\d{9}","\\d{9}",,,"123456789",,,[-1,9]],[,,"NA","NA",,,"123456789",,,[-1]],[,,"NA","NA",,,"123456789",,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"\\d{9}","\\d{9}",,,"123456789",,,[9]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],
"001",979,,,,,,,,1,[[,"(\\d)(\\d{4})(\\d{4})","$1 $2 $3"]],,[,,"NA","NA",,,,,,[-1]],,,[,,"NA","NA",,,,,,[-1]],[,,"NA","NA",,,,,,[-1]],1,,[,,"NA","NA",,,,,,[-1]]]};/*

 Copyright (C) 2010 The Libphonenumber Authors.

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
*/
function P(){this.a={}}P.a=function(){return P.c?P.c:P.c=new P};
var za={0:"0",1:"1",2:"2",3:"3",4:"4",5:"5",6:"6",7:"7",8:"8",9:"9","\uff10":"0","\uff11":"1","\uff12":"2","\uff13":"3","\uff14":"4","\uff15":"5","\uff16":"6","\uff17":"7","\uff18":"8","\uff19":"9","\u0660":"0","\u0661":"1","\u0662":"2","\u0663":"3","\u0664":"4","\u0665":"5","\u0666":"6","\u0667":"7","\u0668":"8","\u0669":"9","\u06f0":"0","\u06f1":"1","\u06f2":"2","\u06f3":"3","\u06f4":"4","\u06f5":"5","\u06f6":"6","\u06f7":"7","\u06f8":"8","\u06f9":"9"},Aa={0:"0",1:"1",2:"2",3:"3",4:"4",5:"5",6:"6",
7:"7",8:"8",9:"9","\uff10":"0","\uff11":"1","\uff12":"2","\uff13":"3","\uff14":"4","\uff15":"5","\uff16":"6","\uff17":"7","\uff18":"8","\uff19":"9","\u0660":"0","\u0661":"1","\u0662":"2","\u0663":"3","\u0664":"4","\u0665":"5","\u0666":"6","\u0667":"7","\u0668":"8","\u0669":"9","\u06f0":"0","\u06f1":"1","\u06f2":"2","\u06f3":"3","\u06f4":"4","\u06f5":"5","\u06f6":"6","\u06f7":"7","\u06f8":"8","\u06f9":"9",A:"2",B:"2",C:"2",D:"3",E:"3",F:"3",G:"4",H:"4",I:"4",J:"5",K:"5",L:"5",M:"6",N:"6",O:"6",P:"7",
Q:"7",R:"7",S:"7",T:"8",U:"8",V:"8",W:"9",X:"9",Y:"9",Z:"9"},Q=RegExp("^[+\uff0b]+"),Ba=RegExp("([0-9\uff10-\uff19\u0660-\u0669\u06f0-\u06f9])"),Ca=RegExp("[+\uff0b0-9\uff10-\uff19\u0660-\u0669\u06f0-\u06f9]"),Da=/[\\\/] *x/,Ea=RegExp("[^0-9\uff10-\uff19\u0660-\u0669\u06f0-\u06f9A-Za-z#]+$"),Fa=/(?:.*?[A-Za-z]){3}.*/,Ga=RegExp("(?:;ext=([0-9\uff10-\uff19\u0660-\u0669\u06f0-\u06f9]{1,7})|[ \u00a0\\t,]*(?:e?xt(?:ensi(?:o\u0301?|\u00f3))?n?|\uff45?\uff58\uff54\uff4e?|[,x\uff58#\uff03~\uff5e]|int|anexo|\uff49\uff4e\uff54)[:\\.\uff0e]?[ \u00a0\\t,-]*([0-9\uff10-\uff19\u0660-\u0669\u06f0-\u06f9]{1,7})#?|[- ]+([0-9\uff10-\uff19\u0660-\u0669\u06f0-\u06f9]{1,5})#)$",
"i"),Ha=RegExp("^[0-9\uff10-\uff19\u0660-\u0669\u06f0-\u06f9]{2}$|^[+\uff0b]*(?:[-x\u2010-\u2015\u2212\u30fc\uff0d-\uff0f \u00a0\u00ad\u200b\u2060\u3000()\uff08\uff09\uff3b\uff3d.\\[\\]/~\u2053\u223c\uff5e*]*[0-9\uff10-\uff19\u0660-\u0669\u06f0-\u06f9]){3,}[-x\u2010-\u2015\u2212\u30fc\uff0d-\uff0f \u00a0\u00ad\u200b\u2060\u3000()\uff08\uff09\uff3b\uff3d.\\[\\]/~\u2053\u223c\uff5e*A-Za-z0-9\uff10-\uff19\u0660-\u0669\u06f0-\u06f9]*(?:;ext=([0-9\uff10-\uff19\u0660-\u0669\u06f0-\u06f9]{1,7})|[ \u00a0\\t,]*(?:e?xt(?:ensi(?:o\u0301?|\u00f3))?n?|\uff45?\uff58\uff54\uff4e?|[,x\uff58#\uff03~\uff5e]|int|anexo|\uff49\uff4e\uff54)[:\\.\uff0e]?[ \u00a0\\t,-]*([0-9\uff10-\uff19\u0660-\u0669\u06f0-\u06f9]{1,7})#?|[- ]+([0-9\uff10-\uff19\u0660-\u0669\u06f0-\u06f9]{1,5})#)?$",
"i"),Ia=/(\$\d)/;function Ja(a){var b=a.search(Ca);0<=b?(a=a.substring(b),a=a.replace(Ea,""),b=a.search(Da),0<=b&&(a=a.substring(0,b))):a="";return a}function Ka(a){return 2>a.length?!1:R(Ha,a)}function La(a){return R(Fa,a)?S(a,Aa):S(a,za)}function Ma(a){var b=La(a.toString());a.c="";a.a(b)}function S(a,b){for(var c=new I,d,e=a.length,f=0;f<e;++f)d=a.charAt(f),d=b[d.toUpperCase()],null!=d&&c.a(d);return c.toString()}function T(a){return null!=a&&isNaN(a)&&a.toUpperCase()in ya}
function Na(a,b,c){if(0==A(b,2)&&null!=b.a[5]){var d=D(b,5);if(0<d.length)return d}var d=D(b,1),e=U(b);if(0==c)return Oa(d,0,e,"");if(!(d in O))return e;a=V(a,d,W(d));b=null!=b.a[3]&&A(b,3).length?3==c?";ext="+A(b,3):null!=a.a[13]?A(a,13)+D(b,3):" ext. "+D(b,3):"";a:{a=(C(a,20)||[]).length&&2!=c?C(a,20)||[]:C(a,19)||[];for(var f,g=a.length,h=0;h<g;++h){f=a[h];var l=ra(f,3);if(!l||!e.search(A(f,3,l-1)))if(l=new RegExp(A(f,1)),R(l,e)){a=f;break a}}a=null}a&&(g=a,a=D(g,2),f=new RegExp(A(g,1)),D(g,5),
g=D(g,4),e=2==c&&null!=g&&0<g.length?e.replace(f,a.replace(Ia,g)):e.replace(f,a),3==c&&(e=e.replace(RegExp("^[-x\u2010-\u2015\u2212\u30fc\uff0d-\uff0f \u00a0\u00ad\u200b\u2060\u3000()\uff08\uff09\uff3b\uff3d.\\[\\]/~\u2053\u223c\uff5e]+"),""),e=e.replace(RegExp("[-x\u2010-\u2015\u2212\u30fc\uff0d-\uff0f \u00a0\u00ad\u200b\u2060\u3000()\uff08\uff09\uff3b\uff3d.\\[\\]/~\u2053\u223c\uff5e]+","g"),"-")));return Oa(d,c,e,b)}function V(a,b,c){return"001"==c?X(a,""+b):X(a,c)}
function U(a){var b=""+A(a,2);return null!=a.a[4]&&A(a,4)?Array(D(a,8)+1).join("0")+b:b}function Oa(a,b,c,d){switch(b){case 0:return"+"+a+c+d;case 1:return"+"+a+" "+c+d;case 3:return"tel:+"+a+"-"+c+d;default:return c+d}}function Pa(a,b){switch(b){case 4:return A(a,5);case 3:return A(a,4);case 1:return A(a,3);case 0:case 2:return A(a,2);case 5:return A(a,6);case 6:return A(a,8);case 7:return A(a,7);case 8:return A(a,21);case 9:return A(a,25);case 10:return A(a,28);default:return A(a,1)}}
function Qa(a,b){return Y(a,A(b,1))?Y(a,A(b,5))?4:Y(a,A(b,4))?3:Y(a,A(b,6))?5:Y(a,A(b,8))?6:Y(a,A(b,7))?7:Y(a,A(b,21))?8:Y(a,A(b,25))?9:Y(a,A(b,28))?10:Y(a,A(b,2))?A(b,18)||Y(a,A(b,3))?2:0:!A(b,18)&&Y(a,A(b,3))?1:-1:-1}function X(a,b){if(null==b)return null;b=b.toUpperCase();var c=a.a[b];if(!c){c=ya[b];if(!c)return null;c=(new H).c(L.f(),c);a.a[b]=c}return c}function Y(a,b){var c=a.length;return 0<ra(b,9)&&-1==x(C(b,9)||[],c)?!1:R(D(b,2),a)}
function Ra(a,b){if(!b)return null;var c=D(b,1);if(c=O[c])if(1==c.length)c=c[0];else a:{for(var d=U(b),e,f=c.length,g=0;g<f;g++){e=c[g];var h=X(a,e);if(null!=h.a[23]){if(!d.search(A(h,23))){c=e;break a}}else if(-1!=Qa(d,h)){c=e;break a}}c=null}else c=null;return c}function W(a){return(a=O[a])?a[0]:"ZZ"}function Sa(a,b){var c=C(b,9)||[],d=C(b,10)||[],e=a.length;if(-1<x(d,e))return 0;d=c[0];return d==e?0:d>e?2:c[c.length-1]<e?3:-1<x(c,e,1)?0:3}
function Ta(a,b,c,d,e){if(!a.length)return 0;a=new I(a);var f;b&&(f=A(b,11));null==f&&(f="NonMatch");var g=a.toString();if(g.length)if(Q.test(g))g=g.replace(Q,""),a.c="",a.a(La(g)),f=1;else{g=new RegExp(f);Ma(a);f=a.toString();if(f.search(g))f=!1;else{var g=f.match(g)[0].length,h=f.substring(g).match(Ba);h&&null!=h[1]&&0<h[1].length&&"0"==S(h[1],za)?f=!1:(a.c="",a.a(f.substring(g)),f=!0)}f=f?5:20}else f=20;d&&B(e,6,f);if(20!=f){if(2>=a.c.length)throw"Phone number too short after IDD";a:{d=a.toString();
if(d.length&&"0"!=d.charAt(0))for(a=d.length,f=1;3>=f&&f<=a;++f)if(b=parseInt(d.substring(0,f),10),b in O){c.a(d.substring(f));c=b;break a}c=0}if(c)return B(e,1,c),c;throw"Invalid country calling code";}if(b&&(f=D(b,10),g=""+f,h=a.toString(),!h.lastIndexOf(g,0))){var l=new I(h.substring(g.length)),g=A(b,1),h=new RegExp(D(g,2));Ua(l,b,null);b=l.toString();if(!R(h,a.toString())&&R(h,b)||3==Sa(a.toString(),g))return c.a(b),d&&B(e,6,10),B(e,1,f),f}B(e,1,0);return 0}
function Ua(a,b,c){var d=a.toString(),e=d.length,f=A(b,15);if(e&&null!=f&&f.length){var g=new RegExp("^(?:"+f+")");if(e=g.exec(d)){var f=new RegExp(D(A(b,1),2)),h=R(f,d),l=e.length-1;b=A(b,16);if(null!=b&&b.length&&null!=e[l]&&e[l].length){if(d=d.replace(g,b),!h||R(f,d))c&&0<l&&c.a(e[1]),a.set(d)}else if(!h||R(f,d.substring(e[0].length)))c&&0<l&&null!=e[l]&&c.a(e[1]),a.set(d.substring(e[0].length))}}}
function Z(a,b,c){if(!T(c)&&0<b.length&&"+"!=b.charAt(0))throw"Invalid country calling code";return Va(a,b,c,!0)}
function Va(a,b,c,d){if(null==b)throw"The string supplied did not seem to be a phone number";if(250<b.length)throw"The string supplied is too long to be a phone number";var e=new I,f=b.indexOf(";phone-context=");if(0<f){var g=f+15;if("+"==b.charAt(g)){var h=b.indexOf(";",g);0<h?e.a(b.substring(g,h)):e.a(b.substring(g))}g=b.indexOf("tel:");e.a(b.substring(0<=g?g+4:0,f))}else e.a(Ja(b));f=e.toString();g=f.indexOf(";isub=");0<g&&(e.c="",e.a(f.substring(0,g)));if(!Ka(e.toString()))throw"The string supplied did not seem to be a phone number";
f=e.toString();if(!(T(c)||null!=f&&0<f.length&&Q.test(f)))throw"Invalid country calling code";f=new M;d&&B(f,5,b);a:{b=e.toString();g=b.search(Ga);if(0<=g&&Ka(b.substring(0,g)))for(var h=b.match(Ga),l=h.length,q=1;q<l;++q)if(null!=h[q]&&0<h[q].length){e.c="";e.a(b.substring(0,g));b=h[q];break a}b=""}0<b.length&&B(f,3,b);b=X(a,c);g=new I;h=0;l=e.toString();try{h=Ta(l,b,g,d,f)}catch(z){if("Invalid country calling code"==z&&Q.test(l)){if(l=l.replace(Q,""),h=Ta(l,b,g,d,f),!h)throw z;}else throw z;}h?
(e=W(h),e!=c&&(b=V(a,h,e))):(Ma(e),g.a(e.toString()),null!=c?(h=D(b,10),B(f,1,h)):d&&(delete f.a[6],f.c&&delete f.c[6]));if(2>g.c.length)throw"The string supplied is too short to be a phone number";b&&(a=new I,c=new I(g.toString()),Ua(c,b,a),2!=Sa(c.toString(),A(b,1))&&(g=c,d&&0<a.toString().length&&B(f,7,a.toString())));d=g.toString();a=d.length;if(2>a)throw"The string supplied is too short to be a phone number";if(17<a)throw"The string supplied is too long to be a phone number";if(1<d.length&&"0"==
d.charAt(0)){B(f,4,!0);for(a=1;a<d.length-1&&"0"==d.charAt(a);)a++;1!=a&&B(f,8,a)}B(f,2,parseInt(d,10));return f}function R(a,b){var c="string"==typeof a?b.match("^(?:"+a+")$"):b.match(a);return c&&c[0].length==b.length?!0:!1};v("intlTelInputUtils",{});v("intlTelInputUtils.formatNumber",function(a,b,c){try{var d=P.a(),e=Z(d,a,b);return Na(d,e,"undefined"==typeof c?0:c)}catch(f){return a}});v("intlTelInputUtils.getExampleNumber",function(a,b,c){try{var d=P.a(),e;a:{if(T(a)){var f=Pa(X(d,a),c);try{if(null!=f.a[6]){var g=D(f,6);e=Va(d,g,a,!1);break a}}catch(h){}}e=null}return Na(d,e,b?2:1)}catch(h){return""}});v("intlTelInputUtils.getExtension",function(a,b){try{return A(Z(P.a(),a,b),3)}catch(c){return""}});
v("intlTelInputUtils.getNumberType",function(a,b){try{var c=P.a(),d;var e=Z(c,a,b),f=Ra(c,e),g=V(c,D(e,1),f);if(g){var h=U(e);d=Qa(h,g)}else d=-1;return d}catch(l){return-99}});
v("intlTelInputUtils.getValidationError",function(a,b){try{var c=P.a(),d;var e=Z(c,a,b),f=U(e),g=D(e,1);if(g in O){var h=V(c,g,W(g));d=Sa(f,A(h,1))}else d=1;return d}catch(l){return"Invalid country calling code"==l?1:"The string supplied did not seem to be a phone number"==l?4:"Phone number too short after IDD"==l||"The string supplied is too short to be a phone number"==l?2:"The string supplied is too long to be a phone number"==l?3:-99}});
v("intlTelInputUtils.isValidNumber",function(a,b){try{var c=P.a(),d=Z(c,a,b),e;var f=Ra(c,d),g=D(d,1),h=V(c,g,f),l;if(!(l=!h)){var q;if(q="001"!=f){var z,va=X(c,f);if(!va)throw"Invalid region code: "+f;z=D(va,10);q=g!=z}l=q}if(l)e=!1;else{var Wa=U(d);e=-1!=Qa(Wa,h)}return e}catch(Xa){return!1}});v("intlTelInputUtils.numberFormat",{E164:0,INTERNATIONAL:1,NATIONAL:2,RFC3966:3});
v("intlTelInputUtils.numberType",{FIXED_LINE:0,MOBILE:1,FIXED_LINE_OR_MOBILE:2,TOLL_FREE:3,PREMIUM_RATE:4,SHARED_COST:5,VOIP:6,PERSONAL_NUMBER:7,PAGER:8,UAN:9,VOICEMAIL:10,UNKNOWN:-1});v("intlTelInputUtils.validationError",{IS_POSSIBLE:0,INVALID_COUNTRY_CODE:1,TOO_SHORT:2,TOO_LONG:3,NOT_A_NUMBER:4});})();

$(document).ready(function() {
	$("#phone").intlTelInput({
		// utilsScript: '/intl-tel-input/build/js/utils.js',
		separateDialCode: true,
		initialCountry: 'AE'
	});
});

//# sourceMappingURL=telephone.js.map
