window.QuickAdd = window.QuickAdd || (function() {
    var QuickAdd = {
        hackForChoices: 'hackForChoices',
        click_add: function (e) {
            e.preventDefault();
            var item = e.target.closest('.relationship');
            var add_forms = (e.target.getAttribute('data-template')) ? item.getElementsByClassName(e.target.getAttribute('data-template')) : item.getElementsByClassName('add-form');

            quickadd_modal.setContent(add_forms.item(0).innerHTML);
            quickadd_modal.setFooterContent('');

            quickadd_modal.addFooterBtn('Add', 'btn btn-primary float-right', function () {
                var forms = this.closest('.tingle-modal-box').getElementsByTagName('form');
                var form = forms.item(0);
                var data = new FormData(form);

                window.request.post(form.getAttribute('action'))
                    .send(data)
                    .end(function (err, res) {
                        if (res.statusCode == 200) {
                            data = JSON.parse(res.text);

                            var option = '<option value="' + data.data.id + '" selected>' + data.data.name + '</option>';
                            var select = item.getElementsByTagName("select");

                            if (select.item(0).getAttribute('multiple')) {
                                select.item(0).innerHTML = select.item(0).innerHTML + option;
                            }
                            else {
                                select.item(0).innerHTML = option;
                            }

                            select.item(0).dispatchEvent(new Event('change'));

                            QuickAdd.hackForChoices(item, data, select);

                            quickadd_modal.close();
                        }
                    });

            });

            quickadd_modal.addFooterBtn('Cancel', 'btn btn-white float-right', function () {
                quickadd_modal.close();
            });

            quickadd_modal.open();
        },

        hackForChoices: function (item, data, select) {
            // Hack until choices.js is fixed for already init'd elements
            // TODO: Fix choices.js in my free time?
            var choiceList = item.getElementsByClassName("choices__inner").item(0).getElementsByClassName("choices__list").item(0);
            var choice = document.createElement("div");
            choice.setAttribute('class', 'choices__item  choices__item--selectable');
            choice.setAttribute('data-id', choiceList.getElementsByClassName("choices__item").length + 1);
            choice.setAttribute('data-value', data.data.id);
            choice.setAttribute('aria-selected', true);
            choice.setAttribute('data-deletable', true);
            choice.innerHTML = data.data.name + '<button type="button" class="choices__button" data-button="">Remove item</button>';

            if (select.item(0).getAttribute('multiple')) {
                choiceList.appendChild(choice);
            }
            else {
                choiceList.innerHTML = choice.outerHTML;
            }
        }
    };

    return QuickAdd;
}());



/*
 Add modal for quick add use
 */
var quick_adds = document.getElementsByClassName("relationship");
if(quick_adds.length > 0) {
    var quickadd_modal = new tingle.modal({
        footer: true,
        stickyFooter: false,
        closeMethods: ['escape'],
        closeLabel: "Cancel",
        cssClass: [],
        onOpen: function() {

        },
        onClose: function() {

        },
        beforeClose: function() {
            return true; // close the modal
        }
    });
}

/*
 Activate click event on add buttons
 */
for (var i = 0, i_length = quick_adds.length; i < i_length; i++) {
    var item = quick_adds.item(i);
    var add_button = item.getElementsByClassName('add-relationship').item(0);
    add_button.addEventListener('click', window.QuickAdd.click_add);
}
