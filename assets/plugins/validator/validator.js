/*
    Validator v3.0.3
    (c) Yair Even Or
    https://github.com/yairEO/validator
*/

;(function(root, factory) {
    if( typeof define === 'function' && define.amd )
        define([], factory);
    else if( typeof exports === 'object' )
        module.exports = factory();
    else
        root.FormValidator = factory();
}(this, function(){
    function FormValidator(texts, settings){
        this.data = {}; // holds the form fields' data

        this.texts = this.extend({}, this.texts, texts || {});
        this.settings = this.extend({}, this.defaults, settings || {})
    }

    FormValidator.prototype = {
        // Validation error texts
        texts : {
            invalid : '유효한 형식의 값이 아닙니다.',                    // inupt is not as expected
            short : ':min 자릿수 이상여야 합니다.',                     // input is too short
            long : ':max 자릿수 이하여야 합니다.',                      // input is too long
            checked : ':attribute 필드는 필수입니다.',                // must be checked
            empty : ':attribute 필드는 필수입니다.',                   // please put something here
            select : ':attribute 필드는 필수입니다.',                   // Please select an option
            number_min : ':min 이상의 숫자여야 합니다.',            // too low
            number_max : ':max 이하의 숫자여야 합니다.',          // too high
            url : '유효한 URL 형식이 아닙니다.',                          // invalid URL
            number : '반드시 숫자여야 합니다.',                         // not a number
            email : '유효한 메일주소 형식이 아닙니다.',                 // email address is invalid
            email_repeat : '메일주소가 일치하지 않습니다.',          // emails do not match
            date : '유효한 날짜 형식이 아닙니다.',                       // invalid date
            time : '유효한 시간 형식이 아닙니다.',                       // invalid time
            password_repeat : '비밀번호가 일치하지 않습니다.',    // passwords do not match
            no_match : '일치하지 않습니다.',                            // no match
            complete : '유효한 형식의 값이 아닙니다.'                  // input is not complete
        },

        // default settings
        defaults : {
            regex : {
                url          : /^(https?:\/\/)?([\w\d\-_]+\.+[A-Za-z]{2,})+\/?/,
                //phone   : /^\+?([0-9]|[-|' '])+$/i,
                phone      : /^(01[016789]{1}|02|0[3-9]{1}[0-9]{1})-?[0-9]{3,4}-?[0-9]{4}$/i,   // 일반전화 + 휴대폰
                tel           : /^(02|0[3-9]{1}[0-9]{1})-?[0-9]{3,4}-?[0-9]{4}$/i,  // 일반전화
                mobile      : /^(01[016789]{1})-?[0-9]{3,4}-?[0-9]{4}$/i,   // 휴대폰
                numeric    : /^[0-9]+$/i,
                alpha       : /^[a-zA-Z]+$/i,
                alphanumeric : /^[a-zA-Z0-9]+$/i,
                /*email        : {
                    illegalChars : /[\(\)\<\>\,\;\:\\\/\"\[\]]/,
                    filter       : /^.+@.+\..{2,6}$/ // exmaple email "steve@s-i.photo"
                }*/
                email : /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            },
            alerts  : true,
            classes : {
                item  : 'field',
                alert : 'alert',
                bad   : 'bad'
            }
        },

        // Tests (per type)
        // each test return "true" when passes and a string of error text otherwise
        tests : {
            sameAsPlaceholder : function( field, data ){
                if( field.getAttribute('placeholder') ) {
                    //return data.value != field.getAttribute('placeholder') || this.texts.empty;
                    return data.value != field.getAttribute('placeholder') || this.texts.empty.replace(':attribute', field.title);
                } else {
                    return true;
                }
            },

            //hasValue : function( value ){
            hasValue : function(field, data){
                //return value ? true : this.texts.empty;
                return data.value ? true : this.texts.empty.replace(':attribute', field.title);
            },

            // 'linked' is a special test case for inputs which their values should be equal to each other (ex. confirm email or retype password)
            linked : function(a, b, type){
                if( b != a && a && b ){
                    // choose a specific message or a general one
                    return this.texts[type + '_repeat'] || this.texts.no_match;
                }

                return true;
            },

            email : function(field, data){
                //if ( !this.settings.regex.email.filter.test( data.value ) || data.value.match( this.settings.regex.email.illegalChars ) ){
                if ( !this.settings.regex.email.test( data.value ) ){
                    return this.texts.email;
                }

                return true;
            },

            // a "skip" will skip some of the tests (needed for keydown validation)
            text : function(field, data){
                var that = this;
                // make sure there are at least X number of words, each at least 2 chars long.
                // for example 'john F kenedy' should be at least 2 words and will pass validation
                if( data.validateWords ){
                    var words = data.value.split(' ');
                    // iterate on all the words
                    var wordsLength = function(len){
                        for( var w = words.length; w--; ) {
                            if( words[w].length < len ) {
                                //return that.texts.short;
                                return that.texts.short.replace(':min', len);
                            }
                        }
                        return true;
                    };

                    if( words.length < data.validateWords || !wordsLength(2) ) {
                        return this.texts.complete;
                    }
                    //return true;
                }

                if( data.lengthRange && data.value.length < data.lengthRange[0] ) {
                    //return this.texts.short;
                    return this.texts.short.replace(':min', data.lengthRange[0]);
                }

                // check if there is max length & field length is greater than the allowed
                if( data.lengthRange && data.lengthRange[1] && data.value.length > data.lengthRange[1] ) {
                    //return this.texts.long;
                    return this.texts.long.replace(':max', data.lengthRange[1]);
                }

                // check if the field's value should obey any length limits, and if so, make sure the length of the value is as specified
                if( data.lengthLimit && data.lengthLimit.length ){
                    while( data.lengthLimit.length ){
                        if( data.lengthLimit.pop() == data.value.length ) {
                            //return true;
                        }
                    }

                    return this.texts.complete;
                }

                if( data.pattern ){
                    var testResult,
                        regexs = data.pattern.split(',');

                    for(var i = 0; i < regexs.length; i++) {
                        testResult = this.testByRegex(data.value, regexs[i]);
                        if(testResult !== true) {
                            return testResult;
                        }
                    }
                }

                return true;
            },

            number : function( field, data ){
                var a = data.value;
                // if not not a number
                if( isNaN(parseFloat(a)) && !isFinite(a) ){
                    return this.texts.number;
                }
                // not enough numbers
                else if( data.lengthRange && a.length < data.lengthRange[0] ){
                    //return this.texts.short;
                    return this.texts.short.replace(':min', data.lengthRange[0]);
                }
                // check if there is max length & field length is greater than the allowed
                else if( data.lengthRange && data.lengthRange[1] && a.length > data.lengthRange[1] ){
                    //return this.texts.long;
                    return this.texts.long.replace(':max', data.lengthRange[1]);
                }
                else if( data.minmax[0] && (a|0) < data.minmax[0] ){
                    //return this.texts.number_min;
                    return this.texts.number_min.replace(':min', data.minmax[0]);
                }
                else if( data.minmax[1] && (a|0) > data.minmax[1] ){
                    //return this.texts.number_max;
                    return this.texts.number_max.replace(':max', data.minmax[1]);
                }

                return true;
            },

            // Date is validated in European format (day,month,year)
            date : function( field, data ){
                var day, A = data.value.split(/[-./]/g), i;

                // if seperater not exists
                if( A.length != 3 ) {
                    if ( String(A).length == 8 ) {
                        A = [ String(A).substr(0, 4), String(A).substr(4, 2), String(A).substr(6, 2) ];
                    } else if (String(A).length == 6) {
                        A = [ String(A).substr(0, 2), String(A).substr(2, 2), String(A).substr(4, 2) ];
                    } else {
                        return this.texts.date;
                    }
                }

                // if there is native HTML5 support:
                if( field.valueAsNumber )
                    return true;

                for( i = A.length; i--; ){
                    if( isNaN(parseFloat( data.value )) && !isFinite(data.value) )
                        return this.texts.date;
                }
                try{
                    //day = new Date(A[2], A[1]-1, A[0]);
                    //if( day.getMonth()+1 == A[1] && day.getDate() == A[0])
                    // korean format (year, month, day)
                    day = new Date(A[0], A[1]-1, A[2]);
                    if( day.getMonth()+1 == A[1] && day.getDate() == A[2])
                        return true;
                    return this.texts.date;
                }
                catch(er){
                    return this.texts.date;
                }
            },

            time : function( field, data ){
                //var pattern = /^([0-1][0-9]|2[0-3]):[0-5][0-9]$/;
                // hh:mm or hh:mm:ss
                var pattern = /^([0-1][0-9]|2[0-3]):?[0-5][0-9](:?[0-5][0-9])?$/;
                if( pattern.test(data.value) )
                    return true;
                else
                    return this.texts.time;
            },

            url : function( field, data ){
                // minimalistic URL validation
                if( !this.settings.regex.url.test(data.value) )
                    return this.texts.url;

                return true;
            },

            hidden : function( field, data ){
                if( data.lengthRange && data.value.length < data.lengthRange[0] ) {
                    //return this.texts.short;
                    return this.texts.short.replace(':min', data.lengthRange[0]);
                }

                if( data.pattern ){
                    if( data.pattern == 'alphanumeric' && !this.settings.regex.alphanumeric.test(data.value) )
                        return this.texts.invalid;
                }

                return true;
            },

            select : function( field, data ){
                //return data.value ? true : this.texts.select;
                return data.value ? true : this.texts.select.replace(':attribute', field.title);
            },

            checkbox : function( field, data ){
                if( field.checked ) return true;

                //return this.texts.checked;
                return this.texts.checked.replace(':attribute', field.title);
            },

            radio : function( field, data, form ){
                if( form.querySelectorAll('[name=' + field.name + ']:checked').length > 0 ) return true;

                //return this.texts.checked;
                return this.texts.checked.replace(':attribute', field.title);
            }
        },

        /**
         * Marks an field as invalid
         * @param  {DOM Object} field
         * @param  {String} text
         * @return {jQuery Object} - The message element for the field
         */
        mark : function( field, text ){
            if( !text || !field )
                return false;

            var that = this;

            // check if not already marked as 'bad' and add the 'alert' object.
            // if already is marked as 'bad', then make sure the text is set again because i might change depending on validation
            var item = this.closest(field, '.' + this.settings.classes.item),
                alert = item.querySelector('.'+this.settings.classes.alert),
                warning;

            if( this.settings.alerts ){
                if( alert )
                    alert.innerHTML = text;
                else{
                    warning = '<div class="'+ this.settings.classes.alert +'">' + text + '</div>';
                    item.insertAdjacentHTML('beforeend', warning);
                }
            }

            item.classList.remove(this.settings.classes.bad);

            // a delay so the "alert" could be transitioned via CSS
            setTimeout(function(){
                item.classList.add( that.settings.classes.bad );
            });

            return warning;
        },

        /* un-marks invalid fields
        */
        unmark : function( field ){
            if( !field ){
                console.warn('no "field" argument, null or DOM object not found');
                return false;
            }

            var fieldWrap = this.closest(field, '.' + this.settings.classes.item);

            if( fieldWrap ){
                var warning = fieldWrap.querySelector('.'+ this.settings.classes.alert);
                fieldWrap.classList.remove(this.settings.classes.bad);
            }

            if( warning )
                warning.parentNode.removeChild(warning);
        },

        /**
         * Normalize types if needed & return the results of the test (per field)
         * @param  {String} type  [form field type]
         * @param  {*}      value
         * @return {Boolean} - validation test result
         */
        testByType : function( field, data, form ){
            data = this.extend({}, data); // clone the data

            var type = data.type;

            if( type == 'tel' )
                data.pattern = data.pattern || 'phone';

            //if( !type || type == 'password' || type == 'tel' || type == 'search' || type == 'file' )
            if( !type || type == 'password' || type == 'tel' || type == 'search' || type == 'file' || type == 'hidden' )
                type = 'text';

            return this.tests[type] ? this.tests[type].call(this, field, data, form) : true;
        },

        testByRegex : function( value, pattern ) {
            var regex, jsRegex;

            // instead of switch case
            if(this.settings.regex[pattern] === undefined) {
                regex = pattern;
            } else {
                regex = this.settings.regex[pattern];
            }

            try{
                jsRegex = new RegExp(regex).test(value);

                if( !value || ( value && !jsRegex ) ){
                    return this.texts.invalid;
                }
            }
            catch(err){
                console.warn(err, field, 'regex is invalid');
                return this.texts.invalid;
            }

            return true;
        },

        prepareFieldData : function(field){
            var nodeName = field.nodeName.toLowerCase(),
                id = Math.random().toString(36).substr(2,9);

            field["_validatorId"] = id;
            this.data[id] = {};

            this.data[id].value = field.value.replace(/^\s+|\s+$/g, "");   // cache the value of the field and trim it
            this.data[id].valid = true;                                                 // initialize validity of field
            this.data[id].type = field.getAttribute('type');                      // every field starts as 'valid=true' until proven otherwise
            this.data[id].pattern = field.getAttribute('pattern');

            // Special treatment
            if( nodeName === "select" )
                this.data[id].type = "select";

            else if( nodeName === "textarea" )
                this.data[id].type = "text";

            /* Gather Custom data attributes for specific validation:
            */
            this.data[id].validateWords  = field.getAttribute('data-validate-words') || 0;
            this.data[id].lengthRange    = field.getAttribute('data-validate-length-range') ? (field.getAttribute('data-validate-length-range')+'').split(',') : [1];
            this.data[id].lengthLimit    = field.getAttribute('data-validate-length') ? (field.getAttribute('data-validate-length')+'').split(',') : false;
            this.data[id].minmax         = field.getAttribute('data-validate-minmax') ? (field.getAttribute('data-validate-minmax')+'').split(',') : false; // for type 'number', defines the minimum and/or maximum for the value as a number.
            this.data[id].validateLinked = field.getAttribute('data-validate-linked');

            return this.data[id];
        },

        /* linkTo or required_if field selector
        */
        fieldSelector : function(form, fieldName) {
            var field;

            if( fieldName.indexOf('#') == 0 )
                field = document.body.querySelectorAll(fieldName);
            else if( form.length )
                field = form.querySelectorAll('[name=' + fieldName + ']');
            else
                field = document.body.querySelectorAll('[name=' + fieldName + ']');

            return field;
        },

        /* required_if field whether to check or pass
        */
        isCheckRequiredIf : function(field) {
            var toField,
                toValue,
                toFieldData,
                required = field.getAttribute('required'),
                form = this.closest(field, 'form');

            this.unmark(field);

            if(required && required.indexOf('_if') != -1) {
                toFieldData = required.split(':')[1].split(',');
                toField = this.fieldSelector(form, toFieldData[0]);
                toValue = toFieldData[1];

                for(var i =0; i < toField.length; i++) {
                    switch (toField[i].type) {
                        case 'radio' :
                        case 'checkbox' :
                            if (toField[i].checked === true && toField[i].value == toValue) {
                                return true;
                            }
                            break;
                        default :
                            if (toField[i].value == toValue) {
                                return true;
                            }
                            break;
                    }
                }
            }

            return false;
        },

        closest : function(el, selector){
            var matchesFn;

            // find vendor prefix
            ['matches','webkitMatchesSelector','mozMatchesSelector','msMatchesSelector','oMatchesSelector'].some(function(fn) {
                if (typeof document.body[fn] == 'function') {
                    matchesFn = fn;
                    return true;
                }
                return false;
            })

            var parent;

            // traverse parents
            while (el) {
                parent = el.parentElement;
                if (parent && parent[matchesFn](selector)) {
                    return parent;
                }
                el = parent;
            }

            return null;
        },

        // MDN polyfill for Object.assign
        extend : function(target, varArgs){
            if( !target )
                throw new TypeError('Cannot convert undefined or null to object');

            var to = Object(target),
                nextKey, nextSource, index;

            for( index = 1; index < arguments.length; index++ ){
                nextSource = arguments[index];

                if( nextSource != null ) // Skip over if undefined or null
                    for( nextKey in nextSource )
                        // Avoid bugs when hasOwnProperty is shadowed
                        if( Object.prototype.hasOwnProperty.call(nextSource, nextKey) )
                            to[nextKey] = nextSource[nextKey];
            }

            return to;
        },

        /**
         * Only allow certain form elements which are actual inputs to be validated
         * @param  {HTMLCollection} form fields Array [description]
         * @return {Array}                            [description]
         */
        filterFormElements : function( fields ){
            var i,
                fieldsToCheck = [];

            for( i = fields.length; i--; ) {
                var isAllowedElement = fields[i].nodeName.match(/input|textarea|select/gi),
                    isRequiredAttirb = fields[i].hasAttribute('required') && fields[i].getAttribute('required').indexOf('_if') == -1,
                    isRequiredIfAttirb = fields[i].hasAttribute('required') && fields[i].getAttribute('required').indexOf('_if') != -1 && this.isCheckRequiredIf(fields[i]),
                    isDisabled = fields[i].hasAttribute('disabled'),
                    isOptional = fields[i].className.indexOf('optional') != -1;

                if( isAllowedElement && (isRequiredAttirb || isRequiredIfAttirb || isOptional) && !isDisabled ) {
                    fieldsToCheck.push(fields[i]);
                }
            }

            return fieldsToCheck;
        },

        /* Checks a single form field by it's type and specific (custom) attributes
        * {DOM Object}     - the field to be checked
        * {Boolean} silent - don't mark a field and only return if it passed the validation or not
        */
        checkField : function( field, silent ){
            // skip testing fields whom their type is not HIDDEN but they are HIDDEN via CSS.
            // add must validate class
            if( field.type !='hidden' && !field.clientWidth && field.className.indexOf('must-validate') < 0 )
                return { valid:true, error:"" }

            field = this.filterFormElements( [field] )[0];

            // if field did not pass filtering or is simply not passed
            if( !field )
                return { valid:true, error:"" }

            //this.unmark( field );

            var linkedTo,
                testResult,
                optional = field.className.indexOf('optional') != -1,
                data = this.prepareFieldData( field ),
                form = this.closest(field, 'form'); // if the field is part of a form, then cache it

            // check if field has any value
            /* Validate the field's value is different than the placeholder attribute (and attribute exists)
            *  this is needed when fixing the placeholders for older browsers which does not support them.
            *  in this case, make sure the "placeholder" jQuery plugin was even used before proceeding
            */

            // first, check if the field even has any value
            testResult = this.tests.hasValue.call(this, field, data);

            // if the field has value, check if that value is same as placeholder
            if( testResult === true )
                testResult = this.tests.sameAsPlaceholder.call(this, field, data);

            data.valid = optional || testResult === true;

            if( optional && !data.value ){
                return { valid:true, error:"" };
            }

            if( testResult !== true )
                data.valid = false;

            // validate by type of field. use 'attr()' is proffered to get the actual value and not what the browsers sees for unsupported types.
            if( data.valid ){
                //testResult = this.testByType(field, data);
                testResult = this.testByType(field, data, form);
                data.valid = testResult === true ? true : false;
            }

            // if this field is linked to another field (their values should be the same)
            if( data.valid && data.validateLinked ){
                linkedTo = this.fieldSelector(form, data['validateLinked']);

                testResult = this.tests.linked.call(this, field.value, linkedTo[0].value, data.type);
                data.valid = testResult === true ? true : false;
            }

            if( !silent ) {
                this[data.valid ? "unmark" : "mark"](field, testResult); // mark / unmark the field
            }

            return {
                valid : data.valid,
                error : data.valid === true ? "" : testResult
            };
        },

        checkAll : function( form ){
            if( !form ){
                console.warn('element not found');
                return false;
            }

            var that = this,
                result = {
                    valid  : true,  // overall form validation flag
                    fields : []     // array of objects (per form field)
                },
                fieldsToCheck= this.filterFormElements( form.elements );
            // get all the input/textareas/select fields which are required or optional (meaning, they need validation only if they were filled)

            fieldsToCheck.forEach(function(elm, i){
                var fieldData = that.checkField(elm);

                // use an AND operation, so if any of the fields returns 'false' then the submitted result will be also FALSE
                result.valid = !!(result.valid * fieldData.valid);

                result.fields.push({
                    field   : elm,
                    error   : fieldData.error,
                    valid   : !!fieldData.valid
                })
            });

            return result;
        },

        checkFirst : function( form, mark ){
            if( !form ){
                console.warn('element not found');
                return false;
            }

            if ( !mark ) {
                this.settings.alerts = false;
            }

            var that = this,
                result = {
                    valid  : true  // overall form validation flag
                },
                fieldsToCheck= this.filterFormElements( form.elements );

            // get all the input/textareas/select fields which are required or optional (meaning, they need validation only if they were filled)
            fieldsToCheck.reverse().forEach(function(elm, i){
                var fieldData = that.checkField(elm);

                if ( !fieldData.valid ) {
                    if ( !mark ) {
                        alert(fieldData.error);
                        //elm.select();
                        elm.focus();
                    }

                    result.valid = false;
                    // break
                    fieldsToCheck.length = 0;
                }
            });

            return result;
        },

        checkRegex : function( value, pattern ) {
            var testResult,
                regexs = pattern.split(','),
                result = {
                    valid  : true  // overall form validation flag
                };

            for(var i = 0; i < regexs.length; i++) {
                testResult = this.testByRegex(value, regexs[i]);
                if(testResult !== true) {
                    result.valid = false;
                }
            }

            if( result.valid === false) {
                alert(this.texts.invalid);
            }

            return result;
        }
    }

    return FormValidator;
}));