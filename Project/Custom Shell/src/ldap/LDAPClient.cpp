//
// Custom Shell [ Pierre & Andrej ]
//

#include "LDAPClient.h"

LDAPClient::LDAPClient() {

    // User on the jump server system
    user = getlogin();
    out("LDAPClient constructor: performing validity check on: [" + user + "]");

    auto* ctrls = new LDAPControlSet;
    ctrls->add(LDAPCtrl(LDAP_CONTROL_MANAGEDSAIT));

    auto* constraints = new LDAPConstraints;
    constraints->setServerControls(ctrls);

    connection = new LDAPConnection("localhost", 389);
    connection->setConstraints(constraints);

//    try{
//
//        String critical_system_to_join("coffe-access");
//        String filter = "cn=" + critical_system_to_join;
//
//
//        connection->bind("cn=readonly,dc=example,dc=org", "readonly", constraints);
//        out("Host: " + connection->getHost());
//        // bool result = lc->compare("cn=readonly,dc=example,dc=org", LDAPAttribute("cn","readonly"));
//        // auto* attrs = new LDAPAttributeList();
//        StringList values;
//        // values.add("ou");
//        values.add("uniqueMember");
//        // attrs->addAttribute(LDAPAttribute("objectClass",values));
//
//
//        LDAPSearchResults* entries = connection->search(
//                "ou=remote-access, dc=example, dc=org",
//                LDAPConnection::SEARCH_SUB,
//                filter,
//                values);
//
//
//        if (entries != nullptr){
//            LDAPEntry *entry = entries->getNext();
//            if(entry != nullptr){
//                out(*entry);
//            }
//            while(entry){
//                try{
//                    entry = entries->getNext();
//                    if(entry != nullptr){
//                        out(*entry);
//                    }
//                    delete entry;
//                }catch(LDAPReferralException& e){
//                    std::cout << "Caught Referral" << std::endl;
//                }
//            }
//        }
//
//        connection->unbind();
//
//    }
//    catch (LDAPException &e) {
//        out("---  ---  ---  ---  ---  ---  ---  ---  ---  caught Exception:");
//        out(e);
//    }

}

LDAPClient::~LDAPClient() {
    delete connection;
}

QueryResult LDAPClient::verify(const String& group){

    out("Verifying on group: " + group);
    out("Eligible users: [");

    try {
        connection->bind("cn=readonly,dc=example,dc=org", "readonly"); // , constraints

        StringList values; values.add("uniqueMember");

        LDAPSearchResults* entries = connection->search(
            "ou=remote-access, dc=example, dc=org",
            LDAPConnection::SEARCH_SUB,
            "cn=" + group,
            values);

        // Parsing:
        if (entries == nullptr) { // Something went wrong: invalid group? compromised system?
            connection->unbind();
            return QueryResult::QUERY_ERR;
        }

        try {
            for (LDAPEntry *entry = entries->getNext(); entry != nullptr; entry = entries->getNext()) {
                // out(*entry);
                const auto& members = entry->getAttributeByName("uniqueMember");
                if (members == nullptr) {
                    connection->unbind();
                    out("]");
                    return QueryResult::REJECTED;
                }

                for (const auto &member : members->getValues()) {

                    // cn=someone, ou=org1, ou=org2, ...
                    auto memberStruct = parse(member, ',');

                    for (auto & s : memberStruct) {

                        // splitting by Equal sign - so we find cn=username
                        auto item = parse(s, '=');
                        if (item.at(0) == "cn") {
                            out(item.at(1));
                            if (item.at(1) == user) {
                                return QueryResult::VERIFIED;
                            }
                        }
                    }
                }
            }

            out("]");

        }
        catch(LDAPReferralException& e){
            out("Caught Referral");
            return QueryResult::QUERY_ERR;
        }

        connection->unbind();
    }
    catch (LDAPException &e) {
        out("Exception:");
        out(e);
        return QueryResult::QUERY_ERR;
    }

    return QueryResult::REJECTED;
}

/**
 * Parse line by
 * @param line simple string.
 * @return vector<string>
 */
StringVec LDAPClient::parse(const String& line, const char delimiter) {

    StringVec res;
    String token;

    for (auto &c : line) {
        if (c == delimiter) {
            if (!token.empty()) {
                res.push_back(token);
                token.clear();
            }
        }
        else {
            token += c;
        }
    }

    if (!token.empty()) {
        res.push_back(token);
    }

    return res;
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



