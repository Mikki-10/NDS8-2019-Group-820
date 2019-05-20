///
/// Custom Shell: the JumpShell
/// Made by Andrej, Pierre and Sebastian
///

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
#include "../thirdparty/ldaplib/LDAPConnection.h"
#include "../thirdparty/ldaplib/LDAPConstraints.h"
#include "../thirdparty/ldaplib/LDAPSearchReference.h"
#include "../thirdparty/ldaplib/LDAPSearchResults.h"
#include "../thirdparty/ldaplib/LDAPAttribute.h"
#include "../thirdparty/ldaplib/LDAPAttributeList.h"
#include "../thirdparty/ldaplib/LDAPEntry.h"
#include "../thirdparty/ldaplib/LDAPException.h"
#include "../thirdparty/ldaplib/LDAPModification.h"
#endif


class LDAPClient {

private:
    /// Performs the underlying LDAP query operations. Imported from ldapc++ lib
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
