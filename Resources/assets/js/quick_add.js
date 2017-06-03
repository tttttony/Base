//Quick Add
var request = require('superagent');

var choices = document.querySelectorAll("select.choices");

var quick_adds = document.getElementsByClassName("relationship");
if(quick_adds.length > 0) {
    var quickadd_modal = new tingle.modal({
        footer: true,
        stickyFooter: false,
        closeMethods: ['escape'],
        closeLabel: "Cancel",
        cssClass: ['custom-class-1', 'custom-class-2'],
        onOpen: function() {
            console.log('modal open');
        },
        onClose: function() {
            console.log('modal close');
        },
        beforeClose: function() {
            return true; // close the modal
        }
    });
}

for (var i = 0, i_length = quick_adds.length; i < i_length; i++) {
    var item = quick_adds.item(i);

    var add_button = item.getElementsByClassName('add-relationship').item(0);

    add_button.addEventListener('click', function (e) {
        e.preventDefault();

        var add_forms = item.getElementsByClassName('add-form');
        quickadd_modal.setContent(add_forms[0].innerHTML);
        quickadd_modal.setFooterContent('');

        quickadd_modal.addFooterBtn('Add', 'btn btn-primary float-right', function(){
            var forms = this.closest('.tingle-modal-box').getElementsByTagName('form');
            var form = forms[0];
            var data = {};

            var elements = form.elements;
            for (e = 0; e < elements.length; e++) {
                if (elements[e].name.length) {
                    data[elements[e].name] = elements[e].value;
                }
            }

            request.post(form.getAttribute('action'))
                .send(data)
                .end(function(err, res){
                    if(res.statusCode == 200) {
                        data = JSON.parse(res.text);

                        var option = document.createElement("option");
                        option.text = data.data.name;
                        option.value = data.data.id;
                        option.selected = true;
                        var select = item.getElementsByTagName("select");
                        select.item(0).appendChild(option);


                        // Hack until choices.js is fixed for already init'd elements
                        // TODO: Fix choices.js in my free time?
                        var choiceList = item.getElementsByClassName("choices__inner").item(0).getElementsByClassName("choices__list").item(0);
                        var choice = document.createElement("div");
                        choice.setAttribute('class', 'choices__item  choices__item--selectable');
                        choice.setAttribute('data-id', choiceList.getElementsByClassName("choices__item").length+1);
                        choice.setAttribute('data-value', data.data.id);
                        choice.setAttribute('aria-selected', true);
                        choice.setAttribute('data-deletable', true);
                        choice.innerHTML = data.data.name+'<button type="button" class="choices__button" data-button="">Remove item</button>';
                        choiceList.appendChild(choice);


                        quickadd_modal.close();
                    }
                });
        });

        quickadd_modal.addFooterBtn('Cancel', 'btn btn-white float-right', function(){
            quickadd_modal.close();
        });

        quickadd_modal.open();
    });
}