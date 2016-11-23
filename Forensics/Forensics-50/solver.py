from scapy.all import *
import base64

pkts = rdpcap("somepang.pcap")
buf = ""
for pkt in pkts:
    if pkt.haslayer(ICMP) and pkt[ICMP].type == 8:
        buf += pkt[ICMP][Raw].load[20:22]

with open("out.jpg", "w") as f:
    f.write(base64.b64decode(buf))
