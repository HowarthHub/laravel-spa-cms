<?php

use App\Models\ContactEnquiryModel;

// --- Index ---

it('lists enquiries for authorised user', function () {
    actingAsSuperAdmin();
    ContactEnquiryModel::factory()->count(3)->create();

    $this->get(route('admin.enquiries.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Enquiries/EnquiryIndexPage')
            ->has('enquiries.data', 3)
            ->has('newCount')
        );
});

it('filters enquiries by status', function () {
    actingAsSuperAdmin();
    ContactEnquiryModel::factory()->count(2)->create(['status' => 'new']);
    ContactEnquiryModel::factory()->read()->create();

    $this->get(route('admin.enquiries.index', ['status' => 'new']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('enquiries.data', 2));
});

it('filters enquiries by search term', function () {
    actingAsSuperAdmin();
    ContactEnquiryModel::factory()->create(['name' => 'John Doe']);
    ContactEnquiryModel::factory()->create(['name' => 'Jane Smith']);

    $this->get(route('admin.enquiries.index', ['search' => 'John']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('enquiries.data', 1));
});

// --- Show ---

it('shows an enquiry and marks it as read', function () {
    actingAsSuperAdmin();
    $enquiry = ContactEnquiryModel::factory()->create(['status' => 'new', 'read_at' => null]);

    $this->get(route('admin.enquiries.show', $enquiry))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Enquiries/EnquiryShowPage')
            ->where('enquiry.id', $enquiry->id)
        );

    expect($enquiry->fresh())
        ->read_at->not->toBeNull()
        ->status->toBe('read');
});

// --- Update ---

it('updates an enquiry with a reply note', function () {
    actingAsSuperAdmin();
    $enquiry = ContactEnquiryModel::factory()->read()->create();

    $this->put(route('admin.enquiries.update', $enquiry), [
        'reply_note' => 'Thank you for reaching out.',
    ])->assertRedirect(route('admin.enquiries.show', $enquiry));

    expect($enquiry->fresh())
        ->reply_note->toBe('Thank you for reaching out.')
        ->status->toBe('replied')
        ->replied_at->not->toBeNull();
});

// --- Destroy ---

it('deletes an enquiry', function () {
    actingAsSuperAdmin();
    $enquiry = ContactEnquiryModel::factory()->create();

    $this->delete(route('admin.enquiries.destroy', $enquiry))
        ->assertRedirect(route('admin.enquiries.index'));

    $this->assertDatabaseMissing('contact_enquiries', ['id' => $enquiry->id]);
});

// --- Bulk Archive ---

it('bulk archives enquiries', function () {
    actingAsSuperAdmin();
    $enquiries = ContactEnquiryModel::factory()->count(3)->create();

    $this->post(route('admin.enquiries.bulk-archive'), [
        'ids' => $enquiries->pluck('id')->toArray(),
    ])->assertRedirect(route('admin.enquiries.index'));

    foreach ($enquiries as $enquiry) {
        expect($enquiry->fresh()->status)->toBe('archived');
    }
});

// --- Auth ---

it('denies index access without view enquiries permission', function () {
    actingAsEditor();

    $this->get(route('admin.enquiries.index'))
        ->assertForbidden();
});

it('denies update without manage enquiries permission', function () {
    actingAsEditor();
    $enquiry = ContactEnquiryModel::factory()->create();

    $this->put(route('admin.enquiries.update', $enquiry), [
        'reply_note' => 'test',
    ])->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get(route('admin.enquiries.index'))
        ->assertRedirect(route('login'));
});
