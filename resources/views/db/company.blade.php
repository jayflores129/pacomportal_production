@extends('layouts.app')

@section('content')
    
    <div class="panel panel-top">
        <div class="grid justify-space-between">
            <div class="col">
            {!! Breadcrumbs::render('db_migration') !!}
            </div>
        </div>
    </div> 

    <div class="tab-panel">
        <a href="/migration/company" class="{{ request()->is('migration/company') ? 'active' : '' }}">Companies</a>
        <a href="/migration/users" class="{{ request()->is('migration/user') ? 'active' : '' }}">Users</a>
    </div>

    <div class="company-table-wrapper">
        <div class="search-section" style="margin-bottom: 20px;max-width: 400px;">

            <p>Search</p>

            <div style="display: flex;justify-content: space-between;">
              <form style="width: calc(100% - 140px);margin: 0;" action="">
                 <input type="text" id="search" class="placeholder" name="search" style="margin: 0;height: 35px;" />
              </form>
            </div>

         </div>
        <table border="1" class="pacom-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Country</th>
                    <th>Address</th>
                    <th>Fax</th>
                    <th>Telephone No.</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($companies as $key => $company)
                    <tr id="{{ count($companies) === $key + 1 ? 'rowEnd' : '' }}">
                        <td>{{ $company->companyID }}</td>
                        <td>{{ $company->companyName }}</td>
                        <td>{{ $company->companyEmail }}</td>
                        <td>{{ $company->companyCountry }}</td>
                        <td>{{ $company->companyAddress1 ?? $company->companyAddress2 }}</td>
                        <td>{{ $company->companyFax }}</td>
                        <td>{{ $company->companyPhone }}</td>
                        <td>
                            <form class="migration-form" action="/migration/company" method="post">
                                @csrf
                                <input type="hidden" value="{{ $company->companyID }}" name="ref_id">
                                <input type="hidden" value="{{ $company->companyEmail ?? '-' }}" name="email">
                                <input type="hidden" value="{{ $company->companyName ?? '-' }}" name="name">
                                <input type="hidden" value="{{ $company->companyCountry }}" name="country">
                                <input type="hidden" value="{{ $company->companyAddress1 ?? $company->companyAddress2 ?? '-' }}" name="address">
                                <input type="hidden" value="{{ $company->companyFax }}" name="fax">
                                <input type="hidden" value="{{ $company->companyPhone }}" name="telephone_no">
                                <button type='submit' class="btn-brand btn-brand-icon btn-brand-secondary btn-sm">
                                    <i class="fa fa-check"></i><span>Add</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pageLinks">
            {{ $companies->links() }}
        </div>

    </div>

@endsection

<style>
    .pacom-table {
        width: 100%;
    }
    .pacom-table td,
    .pacom-table th {
        padding: 5px 10px;
        font-size: 14px;
    }

    .pacom-table tr:hover {
        background: #f2f2f2;
    }

    .company-table-wrapper {
        background: #fff;
        padding: 20px;
        margin-bottom: 20px;
    }

    .tab-panel a:hover,
    .tab-panel a.active {
        border-bottom-color: #5c8cb6;
        color: #5c8cb6;
    }
    .tab-panel a {
        padding: 7px 6px;
        color: #000;
        font-weight: 900;
        border-bottom: 4px solid transparent;
    }
    .tab-panel {
        background: #fff;
        padding: 10px 0;
        margin-bottom: 20px;
        box-shadow: 0px 0px 11px #d2d2d2cc;
        display: flex;
        padding-bottom: 0;
        padding-left: 10px;
    }
    .load-more-button {
        margin-top: 20px;
        padding: 10px !important;
        width: fit-content;
        justify-content: center;
    }

    .pageLinks {
    padding: 10px 0;
    display: flex;
    justify-content: flex-end;
}
</style>

@section('js')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Array.from(document.getElementsByClassName('migration-form')).forEach(elem => {
        elem.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);

            const res = await fetch(e.target.action, {
                method: 'post',
                body: formData,
            })

            const json = await res.json();

            if (json.success === false && json.exists === 'email') {
                const { isConfirmed } = await Swal.fire({
                    title: "Email already exist",
                    text: "That thing is still around?",
                    icon: "question"
                });

                console.log({isConfirmed})
            }

            

            console.log(json);
        })
    });

    const pageLink = document.getElementById('pageLink');
    pageLink?.setAttribute('data-scroll-position', this.scrollY)

    document.addEventListener('scroll', (e) => {
        pageLink?.setAttribute('data-scroll-position', this.scrollY)
    })

    pageLink?.addEventListener('click', (ev) => {
            ev.preventDefault();
            const {scrollPosition} = ev.target.dataset;
            window.location.href = ev.target.href + '&scrollPosition=' + scrollPosition;
        })
</script>
@stop
