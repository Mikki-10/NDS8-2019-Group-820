//
// Custom Shell [ Pierre & Andrej ]
//

#ifndef NDS_SHELLCOMMAND_H
#define NDS_SHELLCOMMAND_H

#include "../header.hpp"

/** accepts words (vector of strings); returns bool, whether the shell should continue */
typedef std::function<bool(StringVec)> Command;

class ShellCommand {

private:
    String m_name;
    String m_description;
    Command m_implementation;

public:
    ShellCommand(const String& name, const String& description, Command function) {
        // interesting. https://en.cppreference.com/w/cpp/utility/move
        m_name = name;
        m_description = description;
        m_implementation = std::move(function);
    }

    bool operator() () {
        return m_implementation(StringVec());
    }

    bool operator() (StringVec& parameter) {
        return m_implementation(parameter);
    }

    String getName() {
        return m_name;
    }

    String getDesc() {
        return m_description;
    }

};

#endif //NDS_SHELLCOMMAND_H
