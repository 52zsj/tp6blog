define(['jquery', 'bootstrap', 'form','bootstrap-table'], function ($, undefined, Form,Table) {
    var Controller = {
        index: function () {
            var tbale = $("#table");
            tbale.bootstrapTable({
                columns: [{
                    field: 'id',
                    title: 'ID'
                }, {
                    field: 'name',
                    title: 'Name'
                }, {
                    field: 'price',
                    title: 'Price'
                }],
                data: [{
                    id: 1,
                    name: 'Item 1',
                    price: '$1'
                }, {
                    id: 2,
                    name: 'Item 2',
                    price: '$2'
                }]
            })
        },
    };
    return Controller;
});
