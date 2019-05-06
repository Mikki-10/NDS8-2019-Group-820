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
            debug(*entry);
            auto system = CritSys();

            const auto& critsysCN = entry->getAttributeByName("cn");
            if (critsysCN != nullptr && critsysCN->getNumValues() == 1) {
                system.setName(*critsysCN->getValues().begin());
            }

            const auto& critsysIP = entry->getAttributeByName("ipNetworkNumber");
            if (critsysIP != nullptr && critsysIP->getNumValues() == 1) {
                system.setAddress(*critsysIP->getValues().begin());
            }

            const auto& critsysDesc = entry->getAttributeByName("description");
            if (critsysDesc != nullptr && critsysDesc->getNumValues() == 1) {
                system.setDescription(*critsysDesc->getValues().begin());
            }

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
