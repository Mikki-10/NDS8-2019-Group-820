//
// Custom Shell [ Pierre & Andrej ]
//

#include "LDAPClient.h"


LDAPClient::LDAPClient() {

    // Setting up LDAP connection
    auto* ctrls = new LDAPControlSet;
    ctrls->add(LDAPCtrl(LDAP_CONTROL_MANAGEDSAIT));

    auto* constraints = new LDAPConstraints;
    constraints->setServerControls(ctrls);

    m_connection = new LDAPConnection(NDS_LDAP_IP_ADDRESS, 389);
    m_connection->setConstraints(constraints);

}


LDAPClient::~LDAPClient() {
    delete m_connection;
}


void LDAPClient::connect() {
    debug("ldap: connecting\n");
    m_connection->bind("cn=readonly,dc=example,dc=org", "readonly");
}


void LDAPClient::disconnect() {
    debug("\nldap: disconnecting");
    m_connection->unbind();
}


StringVec LDAPClient::getRemoteAccessGroups() {

    StringVec grouplist = StringVec();

    StringList values; values.add("cn");
    LDAPSearchResults* entries = m_connection->search(
            "ou=remote-access, dc=example, dc=org",
            LDAPConnection::SEARCH_SUB, "cn=*", values);

    // Parsing:
    if (entries == nullptr) { // No groups present? TODO: is it OK?
        return grouplist;
    }

    try {
        for (LDAPEntry *entry = entries->getNext(); entry != nullptr; entry = entries->getNext()) {
            const auto& cns = entry->getAttributeByName("cn");
            if (cns == nullptr) continue;
            for (const auto &value : cns->getValues()) {
                grouplist.push_back(value);
            }
        }
    }
    catch(LDAPReferralException& e) {
        debug("Caught Referral");
        return StringVec();
    }

    return grouplist;
}


CritSysContainer LDAPClient::findSystemsByDN(const String &baseDN) {

    CritSysContainer res = CritSysContainer();

    LDAPSearchResults* entries = m_connection->search(baseDN, LDAPConnection::SEARCH_SUB, "cn=*");
    try {
        for (LDAPEntry *entry = entries->getNext(); entry != nullptr; entry = entries->getNext()) {
            // debug(*entry);
            auto system = CritSys();

            const auto& critsysCN = entry->getAttributeByName("cn");
            if (critsysCN != nullptr && critsysCN->getNumValues() == 1) {
                system.setName(*critsysCN->getValues().begin()); // had no other option to fetch out the name;
            }

            // TODO: ip address / other important information
            res.add(system);

        }
    }
    catch(LDAPReferralException& e){
        debug("Caught Referral");
        return res;
    }

    return res;
}


CritSysContainer LDAPClient::getAccessibleSystems(const String &user, const String &group) {

    // debug("Running LDAPClient::getAccessibleSystems on " + user + ", " + group);

    CritSysContainer container = CritSysContainer();
    bool isUserInGroup = false;

    StringList values;
    values.add("ou");           // ou -> to get the group of critical systems
    values.add("uniqueMember"); // uniqueMember -> to know whether the user is present in the group

    LDAPSearchResults* entries = m_connection->search(
        "ou=remote-access, dc=example, dc=org", LDAPConnection::SEARCH_SUB,
        "cn=" + group, values);

    // Parsing:
    if (entries == nullptr) { // Something went wrong
        return container;
    }

    try {
        for (LDAPEntry *entry = entries->getNext(); entry != nullptr; entry = entries->getNext()) {
            // out(*entry);
            // out("");

            // Get member list of this group
            const auto& members = entry->getAttributeByName("uniqueMember");
            if (members != nullptr) {
                for (const auto &member : members->getValues()) {
                    // cn=someone, ou=org1, ou=org2, ...
                    auto memberVec = parse(member, ',');
                    for (const auto &item : memberVec) {
                        // splitting by Equal sign - so we find cn=username
                        auto i = parse(item, '=');
                        if (i.at(0) == "cn" && i.at(1) == user) {
                            isUserInGroup = true;  // the current user is inside the member list
                        }
                    }
                }
            }

            if (isUserInGroup) {
                const auto& sysGroupAttribute = entry->getAttributeByName("ou");
                if (sysGroupAttribute != nullptr) {
                    for (const auto& baseDN : sysGroupAttribute->getValues()) {
                        container.addAll(this->findSystemsByDN(baseDN));
                    }
                }
            }
        }
    }
    catch(LDAPReferralException& e){
        debug("Caught Referral");
        return container;
    }

    return container;
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



















//out("LDAPClient constructor: performing validity check on: [" + user + "]");



//    try{
//        String critical_system_to_join("coffe-access");
//        String filter = "cn=" + critical_system_to_join;
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
//        LDAPSearchResults* entries = connection->search(
//                "ou=remote-access, dc=example, dc=org",
//                LDAPConnection::SEARCH_SUB,
//                filter,
//                values);
//
//        if (entries != nullptr){
//            LDAPEntry *entry = entries->getNext();
//            if(entry != nullptr){ out(*entry); }
//            while(entry){
//                try{
//                    entry = entries->getNext();
//                    if(entry != nullptr){ out(*entry); }
//                    delete entry;
//                }catch(LDAPReferralException& e){
//                    std::cout << "Caught Referral" << std::endl;
//                }
//            }
//        }
//        connection->unbind();
//    }
//    catch (LDAPException &e) {
//        out("---  ---  ---  ---  ---  ---  ---  ---  ---  caught Exception:");
//        out(e);
//    }



