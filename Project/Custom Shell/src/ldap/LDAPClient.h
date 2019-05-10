//
// Custom Shell [ Pierre & Andrej ]
//

#ifndef NDS_LDAPCLIENT_H
#define NDS_LDAPCLIENT_H

#include "../header.hpp"

#define LDAP_DEPRECATED 1
#include <ldap.h>
#include <unistd.h>
#include <sstream>

// LDAP lib replaced by modern C++ library
#include "LDAPConnection.h"
#include "LDAPConstraints.h"
#include "LDAPSearchReference.h"
#include "LDAPSearchResults.h"
#include "LDAPAttribute.h"
#include "LDAPAttributeList.h"
#include "LDAPEntry.h"
#include "LDAPException.h"
#include "LDAPModification.h"

#include "../shell/CritSys.h"

#ifdef WINDOWS_DEVELOPMENT
#include "../ldaplib/LDAPConnection.h"
#include "../ldaplib/LDAPConstraints.h"
#include "../ldaplib/LDAPSearchReference.h"
#include "../ldaplib/LDAPSearchResults.h"
#include "../ldaplib/LDAPAttribute.h"
#include "../ldaplib/LDAPAttributeList.h"
#include "../ldaplib/LDAPEntry.h"
#include "../ldaplib/LDAPException.h"
#include "../ldaplib/LDAPModification.h"
#endif


class LDAPClient {

private:
    /** Performs the underlying LDAP query operations. Imported from ldapc++ lib */
    LDAPConnection *m_connection;
    String m_ldap_ip_address;
    int m_ldap_port;

    StringVec parse(const String& line, char delimiter);
    CritSysContainer findSystemsByDN(const String &baseDN);

public:
    LDAPClient();
    ~LDAPClient();

    void connect();
    void disconnect();

    StringVec getRemoteAccessGroups();
    CritSysContainer getAccessibleSystems(const String& user, const String& group);

};


#endif //NDS_LDAPCLIENT_H
