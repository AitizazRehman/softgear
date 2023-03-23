
   export default {
    methods: {
      can(permissionName) {
        return permissions && (permissions.indexOf(permissionName) !== -1) ;
      },
    },
  };