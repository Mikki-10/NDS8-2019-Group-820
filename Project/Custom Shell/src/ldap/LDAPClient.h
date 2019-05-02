//
// Custom Shell [ Pierre & Andrej ]
//

#ifndef NDS_LDAPCLIENT_H
#define NDS_LDAPCLIENT_H

#include "../header.hpp"

// If lucky, will be replaced by some modern C++ ldap library
// https://linux.die.net/man/3/ldap
// https://www.openldap.org/lists/openldap-technical/201104/msg00030.html

#define LDAP_DEPRECATED 1
#include <ldap.h>
#include <unistd.h>
#include <sstream>

// The ldap_search() routine is deprecated in favor of the
// ldap_search_ext() routine.  The ldap_search_s() and ldap_search_st()
// routines are deprecated in favor of the ldap_search_ext_s() routine.

#include "LDAPConnection.h"
#include "LDAPConstraints.h"
#include "LDAPSearchReference.h"
#include "LDAPSearchResults.h"
#include "LDAPAttribute.h"
#include "LDAPAttributeList.h"
#include "LDAPEntry.h"
#include "LDAPException.h"
#include "LDAPModification.h"


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

enum QueryResult {
    REJECTED = 0,
    VERIFIED = 1,
    QUERY_ERR = -1
};

class LDAPClient {

private:
    LDAPConnection *connection;
    String user;

    StringVec parse(const String& line, const char delimiter);

public:
    LDAPClient();
    ~LDAPClient();
    QueryResult verify(const String& group);

};


#endif //NDS_LDAPCLIENT_H
