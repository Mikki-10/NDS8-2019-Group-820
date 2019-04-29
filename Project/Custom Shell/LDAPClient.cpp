//
// Custom Shell [ Pierre & Andrej ]
//

#include "LDAPClient.h"

// https://github.com/fduckart/uh-cpp-ldap

/*
 *
 *
 *     printf("{\n");


    LDAP *ldap_ptr;

    {   // Step 1: Initialise the handler object (LDAP *) and bind to server
        ldap_initialize(&ldap_ptr, "ldap://127.0.0.1:389");
        int ldap_version = 3;
        int res;

        res = ldap_set_option(ldap_ptr, LDAP_OPT_PROTOCOL_VERSION, &ldap_version);
        if (LDAP_SUCCESS != res) emergency_exit(res);

        // https://linux.die.net/man/3/ldap_bind
        res = ldap_simple_bind_s(ldap_ptr, "cn=readonly,dc=example,dc=org", "readonly");
        if (LDAP_SUCCESS != res) emergency_exit(res);
    }

    {   // Step 2: Make a query
        int msgid = ldap_search(ldap_ptr, getlogin(), LDAP_SCOPE_BASE, "system=*" , NULL, 1);



        //    struct timeval *timeout;
        //    timeout=NULL;
        //    int resulttype;
        //    resulttype = ldap_result(ldap,msgid, 1, timeout,result);
        //    LDAPMessage *first_message;
        //    first_message=ldap_first_message(ldap,*result);
        //    int *errcodep;
        //    char **matcheddnp;
        //    char **errmsgp;
        //    char ***referralsp;
        //    LDAPControl ***serverctrlsp;
        //    int freeit;
        //    int parsed_result;
        //    parsed_result = ldap_parse_result(ldap,*result,errcodep,matcheddnp,errmsgp,referralsp,serverctrlsp,freeit);
        //    printf("%d",parsed_result);
    }
    printf("}\n");
 */


/*
 * void ldap_debug(LDAP *ptr) {

    // https://linux.die.net/man/3/ldap_set_option
    printf("LDAP Handler:\n");
    int res;
    if (LDAP_SUCCESS == ldap_get_option(ptr, LDAP_OPT_PROTOCOL_VERSION, &res)) {
        printf("\tProtocol version: %d\n", res);
    }
    printf("\n");

}

void emergency_exit(int ldap_error_number) {
    // I need (ldap *) here
    // https://linux.die.net/man/3/ldap_error
    char *print_result = ldap_err2string(ldap_error_number);
    printf("LDAP Error: %s\n", print_result);
}
 */