//
// Custom shell [ Pierre & Andrej ]
//

#ifndef NDSTEST_CRITSYS_H
#define NDSTEST_CRITSYS_H

#include "../header.hpp"

class CritSys {

private:
    String name;
    String inet_address;
    // Posibility to add data

public:
    CritSys() {
        this->name = "";
        this->inet_address = "";
    }

    CritSys(const String& name, const String& inet_address) {
        this->name = name;
        this->inet_address = inet_address;
    }

    void setName(const String& p_name) {
        this->name = p_name;
    }

    void setAddress(const String& p_inet_address) {
        this->inet_address = p_inet_address;
    }

    const String& getName() {
        return this->name;
    }

    const String& getAddr() {
        return this->inet_address;
    }

};

// ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---  ---

class CritSysContainer {

private:
    std::map<String, CritSys> m_systems;

public:
    CritSysContainer() {
        m_systems = std::map<String, CritSys>();
    }

    void add(CritSys system) {
        m_systems.insert(std::make_pair(system.getName(), system));
    }

    void addAll(CritSysContainer container) {
        for (std::pair<String, CritSys> p : container.m_systems) {
            this->m_systems.insert(p);
        }
    }

    int size() {
        return m_systems.size();
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
        StringVec res = StringVec();
        for (std::pair<String, CritSys> p : m_systems) {
            res.push_back(p.second.getName());
        }
        return res;
    }

};

#endif //NDSTEST_CRITSYS_H
