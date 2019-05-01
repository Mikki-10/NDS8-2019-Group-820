//
// Custom Shell [ Pierre & Andrej ]
//

#include "LDAPClient.h"

LDAPClient::LDAPClient() {

    auto* ctrls = new LDAPControlSet;
    ctrls->add(LDAPCtrl(LDAP_CONTROL_MANAGEDSAIT));

    auto* cons = new LDAPConstraints;
    cons->setServerControls(ctrls);

    LDAPConnection *lc = new LDAPConnection("localhost", 389);
    lc->setConstraints(cons);

    try{

        lc->bind("cn=readonly,dc=example,dc=org", "readonly", cons);
        //out("Host: " + lc->getHost());
        // bool result = lc->compare("cn=readonly,dc=example,dc=org", LDAPAttribute("cn","readonly"));
        // auto* attrs = new LDAPAttributeList();
        StringList values;
        values.add("ou");
        values.add("uniqueMember");
        // attrs->addAttribute(LDAPAttribute("objectClass",values));


        LDAPSearchResults* entries = lc->search(
                "ou=remote-access, dc=example, dc=org",
                LDAPConnection::SEARCH_SUB,
                "cn=*",
                values);


        if (entries != nullptr){
            LDAPEntry *entry = entries->getNext();
            if(entry != nullptr){
                out(*entry);
            }
            while(entry){
                try{
                    entry = entries->getNext();
                    if(entry != nullptr){
                        out(*entry);
                    }
                    delete entry;
                }catch(LDAPReferralException& e){
                    std::cout << "Caught Referral" << std::endl;
                }
            }
        }

        lc->unbind();
        delete lc;
    }
    catch (LDAPException &e) {
        out("---  ---  ---  ---  ---  ---  ---  ---  ---  caught Exception:");
        out(e);
    }

}

LDAPClient::~LDAPClient() {
}

void LDAPClient::test() {
    out("{");
    out("}");
}




























//    LDAP *ldap_ptr;
//
//    {   // Step 1: Initialise the handler object (LDAP *) and bind to server
//        ldap_initialize(&ldap_ptr, "ldap://127.0.0.1:389");
//        int ldap_version = 3;
//        int res;
//
//        ldap_debug(ldap_ptr);
//
//        res = ldap_set_option(ldap_ptr, LDAP_OPT_PROTOCOL_VERSION, &ldap_version);
//        if (LDAP_SUCCESS != res) emergency_exit(res);
//
//        ldap_debug(ldap_ptr);
//
//        // https://linux.die.net/man/3/ldap_bind
//        res = ldap_simple_bind_s(ldap_ptr, "cn=readonly,dc=example,dc=org", "readonly");
//        if (LDAP_SUCCESS != res) emergency_exit(res);
//    }
//
//    {   // Step 2: Make a query
//        int msgid = ldap_search(ldap_ptr, getlogin(), LDAP_SCOPE_BASE, "system=*" , NULL, 1);
//        //    struct timeval *timeout;
//        //    timeout=NULL;
//        //    int resulttype;
//        //    resulttype = ldap_result(ldap,msgid, 1, timeout,result);
//        //    LDAPMessage *first_message;
//        //    first_message=ldap_first_message(ldap,*result);
//        //    int *errcodep;
//        //    char **matcheddnp;
//        //    char **errmsgp;
//        //    char ***referralsp;
//        //    LDAPControl ***serverctrlsp;
//        //    int freeit;
//        //    int parsed_result;
//        //    parsed_result = ldap_parse_result(ldap,*result,errcodep,matcheddnp,errmsgp,referralsp,serverctrlsp,freeit);
//        //    printf("%d",parsed_result);
//    }
//
//void LDAPClient::emergency_exit(int ldap_error_number) {
//    // https://linux.die.net/man/3/ldap_error
//    char *print_result = ldap_err2string(ldap_error_number);
//    out("LDAP Error:" + String(print_result));
//}
//
//void LDAPClient::ldap_debug(LDAP *ptr) {
//
//    // https://linux.die.net/man/3/ldap_set_option
//    out("LDAP Handler:");
//    int res;
//    if (LDAP_SUCCESS == ldap_get_option(ptr, LDAP_OPT_PROTOCOL_VERSION, &res)) {
//        out("\tProtocol version: " + str(res));
//    }
//
//}



