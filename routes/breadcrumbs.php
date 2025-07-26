<?php


// Home
Breadcrumbs::register('home', function($breadcrumbs)
{
    $breadcrumbs->push('Home', route('home'));
});

// Home > Customers
Breadcrumbs::register('customers', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Customers', url('admin/customers'));
});


// Home > Customers > Add New
Breadcrumbs::register('rootcause', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Root Causes', url('admin/rootcause'));
    //$breadcrumbs->push('New Root Causes', url('admin/rootcause/create'));
});

// Home > Customers > Add New
Breadcrumbs::register('itemstatus', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Item Status', url('admin/itemstatus'));
    //$breadcrumbs->push('New Root Causes', url('admin/rootcause/create'));
});



// Home > Customers > Add New
Breadcrumbs::register('addCustomer', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Users', url('admin/users'));
    $breadcrumbs->push('New User', url('admin/create'));
});


// Home > Customers > Add New
Breadcrumbs::register('viewCustomer', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Customers', url('admin/customers'));
    $breadcrumbs->push('View Customer', url('admin/customers/{id}'));
});


// Home > Companies 
Breadcrumbs::register('allCompany', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Companies', url('admin/companies'));

});

// Home > Companies > View
Breadcrumbs::register('viewCompany', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Companies', url('admin/companies'));
    $breadcrumbs->push('Company Information', url('admin/companies/{id}'));
});

// Home > Companies > View
Breadcrumbs::register('createCompany', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Companies', url('admin/companies'));
    $breadcrumbs->push('New Company', url('admin/companies/create'));
});



// Home > Companies > Add
Breadcrumbs::register('addCompany', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Companies', url('admin/companies'));
    $breadcrumbs->push('New Company', url('admin/companies/create'));
});


// Home > Firmwares 
Breadcrumbs::register('firmwares', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Software/Firmware', url('firmwares'));
});


// Home > Firmwares 
Breadcrumbs::register('firmware', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Software/Firmware', url('firmwares'));
    $breadcrumbs->push('View', url('firmwares/{id}'));
});

// Home > Firmwares > Add New
Breadcrumbs::register('addFirmwares', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Software/Firmware', url('firmwares'));
    $breadcrumbs->push('Upload', url('/firmwares/create'));
});

// Home > Firmwares > Update Firmare name
Breadcrumbs::register('updateFirmwares', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Software/Firmware', url('firmwares'));
    $breadcrumbs->push('Update', url('/firmwares/edit-category/{id}'));
});



// Home > Technical Docs 
Breadcrumbs::register('docs', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Technical Documentation', url('technical-documents'));
});

// Home > Technical Docs > View
Breadcrumbs::register('viewTechDocs', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
 $breadcrumbs->push('Technical Documentation', url('technical-documentation'));
    $breadcrumbs->push('View', url('/technical-documentation/{id}'));
});


// Home > Technical Docs > Add New
Breadcrumbs::register('addDocs', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
 $breadcrumbs->push('Technical Documentation', url('technical-documentation'));
    $breadcrumbs->push('Upload', url('/technical-documentation/create'));
});

// Home > Technical Docs > Edit
Breadcrumbs::register('editDocs', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Technical Documentation', url('technical-documentation'));
    $breadcrumbs->push('Edit', url('/technical-documentation/edit-category/{id}'));
});



// Home > Certificates
Breadcrumbs::register('certificates', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Certificates', url('certificates'));
});

// Home > Firmwares  > view
Breadcrumbs::register('viewCertificate', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Certificates', url('certificates'));
    $breadcrumbs->push('View', url('files/{id}'));
});

// Home > Certificates > Add New
Breadcrumbs::register('addCertificate', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Certificates', url('certificates'));
    $breadcrumbs->push('Upload', url('/certificates/create'));
});

// Home > Certificates > Edit
Breadcrumbs::register('editCertificate', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Certificates', url('certificates'));
    $breadcrumbs->push('Edit', url('/certificates/edit-name/{id}'));
});


// Home > Repairs
Breadcrumbs::register('repairs', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Tickets', url('repairs'));
});

// Home > Repairs > Add New
Breadcrumbs::register('addRepairs', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Tickets', url('repairs'));
    $breadcrumbs->push('New Ticket', url('/repairs/create'));
});


// Home > Repairs > show
Breadcrumbs::register('showRepairs', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Tickets', url('repairs'));
    $breadcrumbs->push('Ticket', url('/repairs/{id}'));
});

// Home > Repairs > Add New
Breadcrumbs::register('allOpenRepairs', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Tickets', url('repairs'));
    $breadcrumbs->push('Open Tickets', url('/search/repairs?status=open'));
});


// Home > Products
Breadcrumbs::register('products', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Products', url('admin/products'));
});


// Home > Products > Add New
Breadcrumbs::register('addProducts', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Products', url('admin/products'));
    $breadcrumbs->push('Create Product', url('admin/products/create'));
});




// Home > Issues
Breadcrumbs::register('issues', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Issues', url('admin/issues'));
});


// Home > Issues > Add New
Breadcrumbs::register('addIssues', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Issues', url('admin/issues'));
    $breadcrumbs->push('Create Issue', url('admin/issues/create'));
});



// Home > Permissions
Breadcrumbs::register('allusers', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All users', url('admin/users'));
});

// Home  > All Users > User Information
Breadcrumbs::register('user', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All users', url('admin/users'));
    $breadcrumbs->push('User Information', url('admin/users/{id}'));
});


// Home  > All Users > User Information
Breadcrumbs::register('editusers', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All users', url('admin/users'));
    $breadcrumbs->push('User Information (Editing)', url('admin/users/{id}/edit'));
});


// Home > Users > Update Permissions
Breadcrumbs::register('userPermission', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All users', url('admin/users'));
    $breadcrumbs->push('Update Permission', url('admin/change-permission'));
});


// Home > Users > Update Permissions
Breadcrumbs::register('pendingUser', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All users', url('admin/users'));
    $breadcrumbs->push('For Approval', url('admin/for-approval'));
});


// Home > Users > Update Permissions
Breadcrumbs::register('settings', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Settings', url('admin/settings'));
});



// Home > Software features
Breadcrumbs::register('softwares', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Tasks', url('admin/softwares'));
});


Breadcrumbs::register('addSoftware', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Tasks', url('admin/softwares'));
    $breadcrumbs->push('New', url('admin/softwares/create'));
});

Breadcrumbs::register('onlyMyTask', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All My Tasks', url('admin/user-task'));
});

Breadcrumbs::register('onlySubmittedTask', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All My Submitted Tasks', url('admin/user-submitted'));
});

Breadcrumbs::register('editSoftware', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('All Tasks', url('admin/softwares'));
    $breadcrumbs->push('Edit', url('admin/softwares/{id}/edit'));
});

Breadcrumbs::register('db_migration', function($breadcrumbs)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Company/User Migration', url('admin/db_migration'));
});