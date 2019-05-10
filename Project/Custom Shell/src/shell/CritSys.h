//
// Custom shell [ Pierre & Andrej ]
//

#ifndef NDS_CRITSYS_H
#define NDS_CRITSYS_H

#include "../header.hpp"

class CritSys {

private:
    String m_name;
    String m_inet_address;
    String m_description;
    // Posibility to add data

public:
    CritSys() {
        m_name = "";
        m_inet_address = "";
        m_description = "";
    }

    void setName(const String& name)                { m_name = name;                  }
    void setAddress(const String& inet_address)     { m_inet_address = inet_address;  }
    void setDescription(const String &description)  { m_description = description;    }

    const String& getName() { return m_name;          }
    const String& getAddr() { return m_inet_address;  }
    const String& getDesc() { return m_description;   }

};

// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

class CritSysContainer {

private:
    std::map<String, CritSys> m_systems; // maybe vector is enough. not sure
    StringVec m_names;

public:
    CritSysContainer() {
        m_systems = std::map<String, CritSys>();
    }

    void add(CritSys system) {
        m_names.push_back(system.getName());
        m_systems.insert(std::make_pair(system.getName(), system));
    }

    void addAll(CritSysContainer container) {
        for (std::pair<String, CritSys> p : container.m_systems) {
            m_names.push_back(p.first);
            m_systems.insert(p);
        }
    }

    CritSys get(String name) {

        auto sys = m_systems.find(name);
        if (sys != m_systems.end()) {
            return sys->second;
        }
        else {
            throw std::invalid_argument("Non-present critical system");
        }

    }

    StringVec getNames() {
        return m_names;
    }

};

#endif //NDS_CRITSYS_H
