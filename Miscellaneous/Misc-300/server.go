package main

import (
    "bytes"
    "fmt"
    "math/rand"
    "net"
    "os"
)

func main() {
    if len(os.Args) !=2 {
        fmt.Printf("Usage: %s <port>\n", os.Args[0])
        os.Exit(1)
    }

    port := os.Args[1]

    var buffer bytes.Buffer
    buffer.WriteString(":")
    buffer.WriteString(port)
    server, _ := net.Listen("tcp", buffer.String())

    for {
        conn, _ := server.Accept()
        if conn != nil{
            go handle(conn)
        }
        conn = nil
    }
}

func handle(conn net.Conn) {
    defer conn.Close()
    var flagBits []int = []int{0, 1, 0, 1, 0, 0, 1, 0,
                               0, 1, 0, 0, 0, 0, 1, 1,
                               0, 0, 1, 1, 0, 0, 1, 1,
                               0, 0, 1, 0, 1, 1, 0, 1,
                               0, 0, 1, 1, 0, 0, 1, 0,
                               0, 0, 1, 1, 0, 0, 0, 0,
                               0, 0, 1, 1, 0, 0, 0, 1,
                               0, 0, 1, 1, 0, 1, 1, 0,
                               0, 0, 1, 0, 1, 1, 0, 1,
                               0, 1, 0, 0, 0, 0, 1, 1,
                               0, 1, 0, 0, 1, 0, 0, 0,
                               0, 1, 0, 1, 0, 0, 1, 0,
                               0, 1, 0, 0, 1, 1, 0, 0,
                               0, 1, 0, 1, 0, 0, 1, 1,
                               0, 1, 0, 0, 0, 1, 0, 0,
                               0, 0, 1, 1, 0, 0, 1, 1,
                               0, 1, 0, 0, 0, 1, 0, 0}

    //Loop through the bits
    for i := 0; i < len(flagBits); i++ {
        //If bit == 1
        if flagBits[i] == 1 {
            //Send odd padded quote
            conn.Write(append([]byte(selectQuote(true))))
            conn.Write([]byte("\n"))
        } else {
            //Send even padded quote
            conn.Write(append([]byte(selectQuote(false))))
            conn.Write([]byte("\n"))
        }
    }
}

func selectQuote(boolean bool) string{
    //Skip past singles and doubles.
    //This function will return a random string from singles if true
    //This function will return a random string from doubles if false
    var singles []string = []string{"R3Jvb3Z5Lgo=",
        "SSdsbCBzd2FsbG93IHlvdXIgc291bCEgSSdsbCBzd2FsbG93IHlvdXIgc291bCEgSSdsbCBzd2FsbG93IHlvdXIgc291bCEgU3dhbGxvdyB0aGlzLgo=",
        "SSBnb3QgaXQsIEkgZ290IGl0ISBJIGtub3cgeW91ciBkYW1uIHdvcmRzLCBhbHJpZ2h0Pwo=",
        "T2ggdGhhdCdzIGp1c3Qgd2hhdCB3ZSBjYWxsIHBpbGxvdyB0YWxrLCBiYWJ5LCB0aGF0J3MgYWxsLgo=",
        "SSBtYXkgYmUgYmFkLi4uIGJ1dCBJIGZlZWwgZ29vb29kLgo=",
        "SSBrbm93IHlvdSdyZSBzY2FyZWQ7IHdlJ3JlIGFsbCBzY2FyZWQsIGJ1dCB0aGF0IGRvZXNuJ3QgbWVhbiB3ZXJlIGNvd2FyZHMuIFdlIGNhbiB0YWtlIHRoZXNlIHNrZWxldG9ucywgd2UgY2FuIHRha2UgdGhlbSwgd2l0aCBzY2llbmNlLgo=",
        "QWxyaWdodCB5b3UgUHJpbWl0aXZlIFNjcmV3aGVhZHMsIGxpc3RlbiB1cCEgWW91IHNlZSB0aGlzPyBUaGlzLi4uIGlzIG15IEJPT01TVElDSyEgVGhlIHR3ZWx2ZS1nYXVnZSBkb3VibGUtYmFycmVsZWQgUmVtaW5ndG9uLiBTLU1hcnQncyB0b3Agb2YgdGhlIGxpbmUuIFlvdSBjYW4gZmluZCB0aGlzIGluIHRoZSBzcG9ydGluZyBnb29kcyBkZXBhcnRtZW50LiBUaGF0J3MgcmlnaHQsIHRoaXMgc3dlZXQgYmFieSB3YXMgbWFkZSBpbiBHcmFuZCBSYXBpZHMsIE1pY2hpZ2FuLiBSZXRhaWxzIGZvciBhYm91dCBhIGh1bmRyZWQgYW5kIG5pbmUsIG5pbmV0eSBmaXZlLiBJdCdzIGdvdCBhIHdhbG51dCBzdG9jaywgY29iYWx0IGJsdWUgc3RlZWwsIGFuZCBhIGhhaXIgdHJpZ2dlci4gVGhhdCdzIHJpZ2h0LiBTaG9wIHNtYXJ0LiBTaG9wIFMtTWFydC4gWW91IGdvdCB0aGF0Pwo="}

    var doubles []string = []string{"SSBiZWxpZXZlIEkgaGF2ZSBtYWRlIGEgc2lnbmlmaWNhbnQgZmluZCBpbiB0aGUgS2FuZGFyaWFuIHJ1aW5zLCBhIHZvbHVtZSBvZiBhbmNpZW50IFN1bWFyaWFuIGJ1cmlhbCBwcmFjdGljZXMgYW5kIGZ1bmVyYXJ5IGluY2FudGF0aW9ucy4gSXQgaXMgZW50aXRsZWQgIk5hdHVydW0gRGUgTW9udHVtIiwgcm91Z2hseSB0cmFuc2xhdGVkOiBCb29rIG9mIHRoZSBEZWFkLiBUaGUgYm9vayBpcyBib3VuZCBpbiBodW1hbiBmbGVzaCBhbmQgaW5rZWQgaW4gaHVtYW4gYmxvb2QuIEl0IGRlYWxzIHdpdGggZGVtb25zIGFuZCBkZW1vbiByZXN1cnJlY3Rpb24gYW5kIHRob3NlIGZvcmNlcyB3aGljaCByb2FtIHRoZSBmb3Jlc3QgYW5kIGRhcmsgYm93ZXJzIG9mIE1hbidzIGRvbWFpbi4gVGhlIGZpcnN0IGZldyBwYWdlcyB3YXJuIHRoYXQgdGhlc2UgZW5kdXJpbmcgY3JlYXR1cmVzIG1heSBsaWUgZG9ybWFudCBidXQgYXJlIG5ldmVyIHRydWx5IGRlYWQuCg==",
    "U2h1dCB1cCwgTGluZGEhCg==",
    "QWZ0ZXIgYWxsLCBJJ20gYSBtYW4gYW5kIHlvdSdyZSBhIHdvbWFuLi4uIGF0IGxlYXN0IGxhc3QgdGltZSBJIGNoZWNrZWQuIEh1aCBodWguCg==",
    "U3VyZSwgSSBjb3VsZCBoYXZlIHN0YXllZCBpbiB0aGUgcGFzdC4gSSBjb3VsZCBoYXZlIGV2ZW4gYmVlbiBraW5nLiBCdXQgaW4gbXkgb3duIHdheSwgSSAqYW0qIGtpbmcuCg==",
    "R29vZC4gQmFkLiBJJ20gdGhlIGd1eSB3aXRoIHRoZSBndW4uCg==",
    "V2VsbCBoZWxsbyBNaXN0ZXIgRmFuY3lwYW50cy4gV2VsbCwgSSd2ZSBnb3QgbmV3cyBmb3IgeW91IHBhbCwgeW91IGFpbid0IGxlYWRpbicgYnV0IHR3byB0aGluZ3MsIHJpZ2h0IG5vdzogSmFjayBhbmQgc2hpdC4uLiBhbmQgSmFjayBsZWZ0IHRvd24uCg==",
    "WW8sIHNoZS1iaXRjaCEgTGV0J3MgZ28hCg==",
    "S2xhYXR1IEJhcmFkYSBOLi4uIE5lY2t0aWUuLi4gTmVja3R1cm4uLi4gTmlja2VsLi4uIEl0J3MgYW4gIk4iIHdvcmQsIGl0J3MgZGVmaW5pdGVseSBhbiAiTiIgd29yZCEgS2xhYXR1Li4uIEJhcmFkYS4uLiBOLi4uCg==",
    "SG9uZXksIHlvdSBnb3QgcmVlZWFsIHVnbHkhCg==",
    "T2gsIHlvdSB3YW5uYSBrbm93PyAnQ2F1c2UgdGhlIGFuc3dlcidzIGVhc3khIEknbSBCQUQgQXNoLi4uIGFuZCB5b3UncmUgR09PRCBBc2ghIFlvdSdyZSBhIGdvb2R5IGxpdHRsZSB0d28tc2hvZXMhIExpdHRsZSBnb29keSB0d28tc2hvZXMhIExpdHRsZSBnb29keSB0d28tc2hvZXMhCg==",
    "TG9vaywgbWF5YmUgSSBkaWRuJ3Qgc2F5IGV2ZXJ5IHNpbmdsZSBsaXR0bGUgdGlueSBzeWxsYWJsZSwgbm8uIEJ1dCBiYXNpY2FsbHkgSSBzYWlkIHRoZW0sIHllYWguCg==",
    "QnVja2xlIHVwIEJvbmVoZWFkLiAnQ2F1c2UgeW91J3JlIGdvaW4nIGZvciBhIHJpZGUhCg==",
    "SSBkb24ndCB3YW50IHlvdXIgYm9vaywgSSBkb24ndCB3YW50IHlvdXIgYnVsbHNoaXQuIEp1c3Qgc2VuZCBtZSBiYWNrIHRvIG15IG93biB0aW1lLCBwcm9udG8sIHRvZGF5LiBDaG9wIGNob3AhCg==",
    "Qm9vbXN0aWNrOiAkMTk5Ljk5LCBTaGVsbHM6IDM5Ljk5LCBab21iaWVzIGhlYWRzIGJsb3dpbmcgb2ZmOiBwcmljZWxlc3MuCg==",
    }

    if boolean == true {
        return singles[rand.Intn(len(singles))]
    } else {
        return doubles[rand.Intn(len(doubles))]
    }
}
