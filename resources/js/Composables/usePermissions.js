import { usePage } from '@inertiajs/vue3';

export function usePermissions() {
    const page = usePage();

    const can = (permission) => {
        return page.props.auth.permissions.includes(permission);
    };

    const hasRole = (role) => {
        return page.props.auth.roles.includes(role);
    };

    return { can, hasRole };
}
