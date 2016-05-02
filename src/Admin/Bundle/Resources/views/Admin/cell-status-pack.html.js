var __view; (__view = (function row(__vars) { print('');




    /** @var GlobalVariables __vars.app */

    /** @var User|Group __vars.entity */

    __vars.rowId = __vars.table + '-id-';
    if(method_exists(__vars.entity, 'getId')) {
        __vars.rowId += __vars.entity.getId();
    }

    __vars.expandable = isset(__vars.searchRequest['expandable']) && is_array(__vars.searchRequest['expandable'])
        ? __vars.searchRequest['expandable']
        : [];
    print('' + "\n"
        + '<div class="'); print (__vars.table); print('-row ');
    print (__vars.rowId); print(' ');
    print (isset(__vars.searchRequest['edit']) && (__vars.searchRequest['edit'] === true || is_array(__vars.searchRequest['edit']) && in_array(__vars.table, __vars.searchRequest['edit']))
        ? 'edit'
        : (isset(__vars.searchRequest['read-only']) && (__vars.searchRequest['read-only'] === false || is_array(__vars.searchRequest['read-only']) && !in_array(__vars.table, __vars.searchRequest['read-only']))
        ? ''
        : 'read-only')); print(' ');
    print (isset(__vars.expandable[__vars.table]) ? 'expandable' : ''); print(' ');
    print (!empty(__vars.classes) ? __vars.classes : ''); print('">' + "\n"
        + '    '); print (__vars.view.render('AdminBundle:Admin:cells+html+php', {'entity' : __vars.entity, 'tables' : __vars.tables, 'table' : __vars.table, 'allGroups' : __vars.allGroups, 'searchRequest' : __vars.searchRequest})); print('' + "\n"
        + '    <label class="checkbox"><input type="checkbox" name="selected"/><i></i></label>' + "\n"
        + '</div>' + "\n"
        + ''); if (isset(__vars.expandable[__vars.table])) { print('' + "\n"
        + '    <div class="expandable ');
        print (!empty(__vars.classes) ? __vars.classes : ''); print('">' + "\n"
            + '    '); print (__vars.view.render('AdminBundle:Admin:cells+html+php', {'entity' : __vars.entity, 'tables' : __vars.expandable, 'table' : __vars.table, 'allGroups' : __vars.allGroups, 'searchRequest' : __vars.searchRequest})); print('' + "\n"
            + '    </div>');
    }}));