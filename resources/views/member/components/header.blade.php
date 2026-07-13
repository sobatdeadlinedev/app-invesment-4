<!-- Fixed Header -->
<div class="fixed-header">
    <div class="header-grid">
        <!-- Right: Profile/Notification Icon -->
        <div class="header-right">
            @include('member.components.profile-dropdown')
        </div>
    </div>
</div>

<style>
.header-grid {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}
</style>